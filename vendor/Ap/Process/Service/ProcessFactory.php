<?php

namespace Ap\Process\Service;

use Zend\ServiceManager\ServiceManagerAwareInterface;
use Zend\ServiceManager\ServiceManager;

use Ap\Process\ProcessPool;
use Ap\Process\Process;
use Control\Process\Video;
use Control\Process\Site;


class ProcessFactory implements ProcessFactoryInterface, ServiceManagerAwareInterface
{
    /**
     *
     * @var ServiceManager
     */
    protected $serviceManager = null;

    /**
     * Строит необходимый объект процесса
     *
     * @param string $name
     * @param ProcessPool $pool
     * @return Process
     */
    public function getProcess($name, ProcessPool $pool, array $options = array())
    {
        $name = strtolower($name);
        $process = new \Ap\Process\Process($pool, $options);

        return $process;
    }

    /**
     *
     * @param ServiceManager $serviceManager
     * @return IndexerFactory
     */
    public function setServiceManager(ServiceManager $serviceManager)
    {
        $this->serviceManager = $serviceManager;

        return $this;
    }
}