<?php

namespace Control;

use Zend\ModuleManager\Feature\ConsoleBannerProviderInterface;
use Zend\ModuleManager\Feature\ConsoleUsageProviderInterface;
use Zend\Console\Adapter\AdapterInterface as Console;
use Zend\Mvc\ModuleRouteListener;
use Zend\Mvc\MvcEvent;

class Module implements ConsoleBannerProviderInterface, ConsoleUsageProviderInterface
{
    public function onBootstrap(MvcEvent $e)
    {
        $e->getApplication()->getServiceManager()->get('translator');
        $eventManager        = $e->getApplication()->getEventManager();
        $moduleRouteListener = new ModuleRouteListener();
        $moduleRouteListener->attach($eventManager);
        
        $eventManager->attach('dispatch.error', function($event){
            $exception = $event->getParam('exception');
            if ($exception) {
                $sm = $event->getApplication()->getServiceManager();
                $service = $sm->get('Application\Service\ErrorHandling');
                $service->logException($exception, $event->getApplication()->getServiceManager());
            }
        });
    }
    
    public function getConsoleUsage(Console $console)
    {
        return array(
        );
    }
    
    public function getConsoleBanner(Console $console)
    {
        return
            "==------------------------------------------------------==\n" .
            "        Welcome to console                     \n" .
            "==------------------------------------------------------==\n" .
            "Version 0.0.1\n"
        ;
    }
    
    public function getConfig()
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
