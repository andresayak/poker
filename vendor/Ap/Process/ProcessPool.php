<?php

namespace Ap\Process;

use Zend\EventManager\EventManagerInterface;
use Zend\EventManager\EventManager;
use Zend\ServiceManager\ServiceManagerAwareInterface;
use Zend\ServiceManager\ServiceManager;

use Zend\Stdlib\SplQueue as Queue;

class ProcessPool implements ServiceManagerAwareInterface
{
    /**
     * Массив информации о контролируемых процессах
     *
     * @var array
     */
    protected $procs = array();

    /**
     * Максимальное количество процессов выполняемых одновременно
     *
     * @var integer
     */
    protected $limit = 1;

    /**
     * Имя порождаемого процесса
     *
     * @var string
     */
    protected $processName = null;

    /**
     * Комманда
     *
     * @var string
     */
    protected $commandName = null;

    /**
     * Опции конкретизирующие процесс
     *
     * @var array
     */
    protected $processOptions = array();

    /**
     *
     * @var EventManagerInterface
     */
    protected $events = null;

    /**
     *
     * @var Queue
     */
    protected $queue = null;

    /**
     *
     * @var ServiceManager
     */
    protected $serviceManager = null;

    /**
     *
     * @param string $command системная комманда
     * @param string $processName имя процесса по которому будет запрошен объект у фабрики
     * @param array $options
     */
    public function __construct($command, $processName, array $options = array())
    {
        $this->commandName = (string) $command;
        $this->processName = (string) $processName;

        $this->queue = new Queue();

        $this->setOptions($options);
    }

    /**
     *
     * @param ServiceManager $serviceManager
     * @return ProcessPool
     */
    public function setServiceManager(ServiceManager $serviceManager)
    {
        $this->serviceManager = $serviceManager;

        return $this;
    }

    /**
     *
     * @return ServiceManager
     * @throws \Exception
     */
    public function getServiceManager()
    {
        if (null === $this->serviceManager) {
            throw new \Exception('service manager not defined');
        }

        return $this->serviceManager;
    }

    /**
     * Установка параметров объекта
     *
     * @param array $params
     * @return ProcessPool
     */
    public function setOptions(array $options)
    {
        foreach ($options as $key => $value) {
            $method = 'set' . ucfirst($key);
            if (method_exists($this, $method)) {
                call_user_func_array(array($this, $method), array($value));
            }
        }

        return $this;
    }

    /**
     *
     * @param array $values
     * @return ProcessPool
     */
    public function setProcessOptions(array $values)
    {
        $this->processOptions = $values;

        return $this;
    }

    /**
     * Установка предела количества одновременно работающих процессов
     *
     * @param integer $limit
     * @return ProcessPool
     */
    public function setLimit($limit)
    {
        $this->limit = (integer) $limit;

        return $this;
    }

    /**
     * Добавляет в очередь параметры нового процесса
     *
     * @param array $arguments - аргументы которые необходимо добавить
     * к строке комманды
     * @param array $descs - массив дескрипторов потоков ввода-вывода
     * @param mixed $pass - доп. параметр который будет передаваться обработчикам событий
     */
    public function run(array $arguments = array(), array $descs = null, $pass = array())
    {
        $process = $this->serviceManager->get('ProcessFactory')->getProcess($this->processName, $this);
        
        $pass = array_merge(
            $pass,
            array(
                'started' => time(),
                'hash' => $process->getHash(),
            )
        );

        $arguments = implode(' ', $arguments);
        $command = $this->commandName;
        if (isset($this->processOptions['path'])) {
            $command = $this->processOptions['path'] . DIRECTORY_SEPARATOR . $command;
        }

        $config = $this->getServiceManager()->get('config');
        if (isset($this->processOptions['sudo'])
            && $this->processOptions['sudo']) {
            if (isset($config['sudo'])) {
                $command = $config['sudo'] . ' ' . $command;
            } else {
                $command = 'sudo ' . $command;
            }
        }

        if (isset($this->processOptions['requiredParams'])) {
            $reqParams = $this->processOptions['requiredParams'];
            if (is_array($reqParams)) {
                $reqParams = implode(' ', $reqParams);
            }
            $arguments = $reqParams . ' ' . $arguments;
        }

        $command = $command . ' ' . $arguments;

        if (null === $descs) {
            $descs = $process->getProcessDescs();
        }
        $this->queue->enqueue(array($command, $descs, $pass));

        $this->check();

        return $process;
    }

    /**
     * Если количество работающих процессов меньше лимита,
     * берет из очереди параметры нового (если таковые имеются) и запускает его
     *
     * @return ProcessPool
     */
    protected function startProcess()
    {
        while (count($this->procs) < $this->limit
            && $this->queue->count()) {

            $data = $this->queue->dequeue();

            $this->getEventManager()->trigger(__FUNCTION__ . '.pre', $this, array('passed' => $data[2]));

            $handle = proc_open($data[0], $data[1], $pipes);
            if (!is_resource($handle)) {
                throw new \Exception("can not create process '$data[0]'");
            }

            $passed = $data[2];
            if (!isset($passed['realStarted'])) {
                $passed['realStarted'] = time();
            }

            $procData = array(
                'passed' => $passed,
                'descriptors' => $data[1],
                'pipes' => $pipes
            );

            $this->procs[] = array(
                'handle' => $handle,
                'data' => $procData
            );

            $this->getEventManager()->trigger(__FUNCTION__ . '.post', $this, $procData);
        }

        return $this;
    }

    /**
     * Выполняет процедуру завершения процесса
     *
     * @param integer $id
     */
    protected function terminate($id)
    {
        $this->getEventManager()->trigger(
            __FUNCTION__ . '.pre',
            $this,
            $this->procs[$id]['data']
        );

        foreach ($this->procs[$id]['data']['descriptors'] as $key => $desc) {
            if ('pipe' === $desc[0]) {
                fclose($this->procs[$id]['data']['pipes'][$key]);
            }
        }

        proc_close($this->procs[$id]['handle']);
        $data = $this->procs[$id]['data'];
        unset($this->procs[$id]);

        $this->getEventManager()->trigger(
            __FUNCTION__ . '.post',
            $this,
            $data
        );
    }

    /**
     *
     * @return EventManagerInterface
     */
    public function getEventManager()
    {
        if (null === $this->events) {
            $this->setEventManager(new EventManager());
        }

        return $this->events;
    }

    /**
     * Проверяет процессы на окончание работы
     *
     * @return integer количество работающих процессов
     */
    public function check()
    {
        foreach ($this->procs as $id => $data) {
            $status = proc_get_status($data['handle']);
            if (false === $status['running']) {
                $this->terminate($id);
            } else {
                $this->getEventManager()->trigger(__FUNCTION__, $this, $data['data']);
            }
        }

        $this->startProcess();

        return count($this->procs);
    }

    /**
     * Чтобы корректно закрыть потоки
     */
    public function __destruct()
    {
        $this->check();
    }

    /**
     *
     * @param EventManagerInterface $events
     * @return ProcessPool
     */
    public function setEventManager(EventManagerInterface $events)
    {
        $events->setIdentifiers(array(
            __CLASS__,
            get_called_class(),
        ));
        $this->events = $events;

        return $this;
    }
}