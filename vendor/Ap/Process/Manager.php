<?php

namespace Ap\Process;

use Zend\EventManager\EventManager;
use Zend\EventManager\EventManagerInterface;
use Zend\ServiceManager\ServiceManagerAwareInterface;
use Zend\ServiceManager\ServiceManager;

class Manager implements ServiceManagerAwareInterface
{
    /**
     *
     * @var ServiceManager
     */
    protected $serviceManager = null;

    /**
     *
     * @var EventManagerInterface
     */
    protected $events = null;

    /**
     * Ассоциативный массив в котором ключ - тип процессов, а значение - их массив
     *
     * @var array
     */
    protected $workers = array();

    /**
     *
     * @param \Zend\ServiceManager\ServiceManager $serviceManager
     * @return \Zend\ServiceManager\ServiceManager
     */
    public function setServiceManager(ServiceManager $serviceManager)
    {
        $this->serviceManager = $serviceManager;

        return $this;
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
     * @param \Zend\EventManager\EventManagerInterface $events
     * @return \Zend\ServiceManager\ServiceManager
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

    /**
     * Управляет запуском и контролирует процессы в количестве
     * и имеющие тип задаными параметрами
     *
     * @param string $type
     * @param integer $count
     * @throws \Exception
     */
    public function process($count = 1)
    {
        $lock = $this->serviceManager->get('ManagingLock');
        while (!$lock->take()) {
            $this->getEventManager()->trigger(
                __FUNCTION__ . '.waiting',
                $this,
                array()
            );

            sleep(30);
        }

        $this->getEventManager()->trigger(
            __FUNCTION__ . '.start',
            $this,
            array('count' => $count)
        );

        $pool = $this->serviceManager->get('ProcessPool');
        $pool->setLimit($count);
        
        for ($i = 0; $i < $count; $i++) {
            $this->workers[] = $pool->run();
            usleep(500000);
        }

        $this->wait();

        $this->getEventManager()->trigger(
            __FUNCTION__ . '.end',
            $this,
            array('count' => $count)
        );

        $lock->release();
    }

    /**
     * Ожидает окончание работы всех процессов
     *
     * @return void
     */
    protected function wait()
    {
        do {
            $completed = true;
            foreach ($this->workers as $type => $processes) {
                foreach ($processes as $process) {
                    if (!$process->isFinished()) {
                        $completed = false;
                    }
                }
            }

            sleep(1);
        } while (!$completed);

        $this->workers = array();
    }
}