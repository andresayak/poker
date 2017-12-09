<?php

namespace Ap\Process;

use Zend\EventManager\EventManager;
use Zend\EventManager\EventManagerInterface;
use Zend\EventManager\Event;

class Process
{
    const STREAM_TYPE_IN = 0;
    const STREAM_TYPE_OUT = 1;
    const STREAM_TYPE_ERR = 2;

    const STATE_NONE = 0;
    const STATE_WAITING_TO_WORK = 1;
    const STATE_WORKING = 2;
    const STATE_WAITING_TO_FINISH = 3;
    const STATE_FINISHED = 4;

    /**
     *
     * @var ProcessPool
     */
    protected $pool = null;

    /**
     *
     * @var EventManagerInterface
     */
    protected $events = null;

    /**
     *
     * @var string
     */
    protected $hash = null;

    /**
     * Состояние процесса
     *
     * @var mixed
     */
    protected $state = self::STATE_NONE;

    /**
     *
     * @var array
     */
    protected $streams = array(
        self::STREAM_TYPE_IN => null,
        self::STREAM_TYPE_OUT => null,
        self::STREAM_TYPE_ERR => null,
    );

    /**
     * Буфферы ввода-вывода
     *
     * @var array
     */
    protected $buffers = array();

    /**
     *
     * @param ProcessPool $pool
     * @param array $options
     */
    public function __construct(ProcessPool $pool, array $options = array())
    {
        $this->pool = $pool;
        $this->hash = md5(microtime() . rand(1, 100000));
        $this->initBuffers();
        $this->setOptions($options);

        $this->state = self::STATE_WAITING_TO_WORK;

        $this->pool->getEventManager()->attach('startProcess.pre', array($this, 'onProcessPreStart'));
        $this->pool->getEventManager()->attach('startProcess.post', array($this, 'onProcessStarted'));
        $this->pool->getEventManager()->attach('check', array($this, 'onProcessRunning'));
        $this->pool->getEventManager()->attach('terminate.pre', array($this, 'onProcessFinishedPre'));
        $this->pool->getEventManager()->attach('terminate.post', array($this, 'onProcessFinishedPost'));
    }

    /**
     * Установка опций объекта
     *
     * @param array $options
     * @return Process
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
     * Отдает содержимое буфера по его имени
     *
     * @param string $name
     * @return string
     * @throws \Exception
     */
    public function getBuffer($name)
    {
        if (!isset($this->buffers[$name])) {
            throw new \Exception('invalid buffer name: ' . $name);
        }

        $this->flushBuffers();

        return $this->buffers[$name];
    }

    /**
     * Отдает состояние в котором находится процесс
     *
     * @return mixed
     */
    public function getState()
    {
        return $this->state;
    }

    /**
     * Возвращает дескрипторы по умолчанию
     *
     * @return array
     */
    public function getProcessDescs()
    {
        return array(
            self::STREAM_TYPE_IN => STDIN,
            self::STREAM_TYPE_OUT => STDOUT,
            self::STREAM_TYPE_ERR => STDERR,
        );
    }

    /**
     *
     * @return \Control\Process\Process
     */
    protected function initBuffers()
    {
        $this->buffers = array(
            self::STREAM_TYPE_IN => '',
            self::STREAM_TYPE_OUT => '',
            self::STREAM_TYPE_ERR => '',
        );

        return $this;
    }

    /**
     *
     * @return string
     */
    public function getHash()
    {
        return $this->hash;
    }

    /**
     * Определяет, закончил ли процесс свою работу
     *
     * @return boolean
     */
    public function isFinished()
    {
        if ($this->state != self::STATE_NONE
            && $this->state != self::STATE_FINISHED) {
            $this->pool->check();
        }

        return $this->state == self::STATE_FINISHED;
    }

    /**
     * Определяет, был ли запущен процесс
     *
     * @return boolean
     */
    public function isStarted()
    {
        return $this->state != self::STATE_NONE;
    }

    /**
     * Вызывается перед стартом нового процесса
     *
     * @param Event $e
     */
    public function onProcessPreStart(Event $e)
    {
        $data = $e->getParam('passed');

        if (isset($data['hash'])
            && $data['hash'] === $this->hash) {

            $this->getEventManager()->trigger(__FUNCTION__, $this, $data);
        }
    }

    /**
     * Вызывается когда новый процесс стартовал
     *
     * @param Event $e
     */
    public function onProcessStarted(Event $e)
    {
        $data = $e->getParam('passed');

        if (isset($data['hash'])
            && $data['hash'] === $this->hash) {

            $pipes = $e->getParam('pipes');
            foreach ($this->getProcessDescs() as $id => $desc) {
                if (is_resource($desc)) {
                    $this->streams[$id] = $desc;
                } elseif(isset($pipes[$id]) && is_resource($pipes[$id])) {
                    $this->streams[$id] = $pipes[$id];
                }

                stream_set_blocking($this->streams[$id], 0);
            }
            $this->state = self::STATE_WORKING;
            $this->getEventManager()->trigger(__FUNCTION__, $this, $data);
        }
    }

    /**
     * Вызывается периодически при работающем процессе
     *
     * @param \Zend\EventManager\Event $e
     */
    public function onProcessRunning(Event $e)
    {
        $data = $e->getParam('passed');
        if (isset($data['hash'])
            && $data['hash'] === $this->hash) {
            $this->flushBuffers();
        }
    }

    /**
     * Проверяет на наличие данных в буферах потоков
     *
     * @return \Control\Process\Process
     */
    protected function flushBuffers()
    {
        $read = array();
        if (is_resource($this->streams[self::STREAM_TYPE_OUT])) {
            $read[] = $this->streams[self::STREAM_TYPE_OUT];
        }
        if (is_resource($this->streams[self::STREAM_TYPE_ERR])) {
            $read[] = $this->streams[self::STREAM_TYPE_ERR];
        }

        $null = null;
        if (count($read)) {
            if (stream_select($read, $null, $null, 0)) {
                foreach ($read as $stream) {
                    $id = array_search($stream, $this->streams, true);
                    if (false !== $id) {
                        $this->buffers[$id] .= stream_get_contents($stream);
                    }
                }
            }
        }

        return $this;
    }

    /**
     * Вызывается после окончания процесса
     * (процесс закончил работу, потоки IO еще открыты)
     *
     * @param Zend\EventManager\Event $e
     */
    public function onProcessFinishedPre(Event $e)
    {
        $data = $e->getParam('passed');

        if (isset($data['hash'])
            && $data['hash'] === $this->hash) {

            $this->state = self::STATE_WAITING_TO_FINISH;
            $this->flushBuffers();
            foreach ($this->streams as &$stream) {
                $stream = null;
            }

            $this->getEventManager()->trigger(__FUNCTION__, $this, $data);
        }
    }

    /**
     * Вызывается когда процесс закончил работу и уничтожен
     *
     * @param Event $e
     */
    public function onProcessFinishedPost(Event $e)
    {
        $data = $e->getParam('passed');

        if (isset($data['hash'])
            && $data['hash'] === $this->hash) {

            $this->state = self::STATE_FINISHED;
            $this->getEventManager()->trigger(__FUNCTION__, $this, $data);
        }
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
     *
     * @param EventManagerInterface $events
     * @return Process
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