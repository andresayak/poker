<?php

namespace Api;

use Zend\Mvc\MvcEvent;

use Zend\Session\Container;
use Zend\Session\SessionManager;
use Zend\Session\Config\SessionConfig AS SessionConfig;

class Module
{
    public function getConfig()
    {
        return include __DIR__ . '/config/module.config.php';
    }
    
    public function getTestResponse()
    {
        return include __DIR__ . '/config/module.config.php';
    }

    public function getAutoloaderConfig()
    {
        return array(
            'Zend\Loader\StandardAutoloader' => array(
                'namespaces' => array(
                    __NAMESPACE__ => __DIR__ . '/src/' . __NAMESPACE__,
                ),
            ),
        );
    }
}
