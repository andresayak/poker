<?php

namespace Game;

use Zend\Mvc\ModuleRouteListener;
use Zend\Mvc\MvcEvent;
use Zend\Session\Container;
use Zend\Http\Request as HttpRequest;
use Zend\View\Model\JsonModel;
use Zend\View\Model\ModelInterface;
class Module
{
    // public function onBootstrap(MvcEvent $e)
    // {
    //     $eventManager = $e->getApplication()->getEventManager();
    //     $e->getApplication()->getEventManager()->attach('dispatch', array($this, 'setLastConnect'), 100);
    // }

    // public function setLastConnect($e)
    // {
    //     $authService = $e->getApplication()->getServiceManager()->get('Auth\Service');
    //     if($authService->getUserRow() and $authService->getUserRow() instanceof \Game\Model\User\Row){
    //         $request = $e->getRequest();
    //         if (!$request instanceof HttpRequest) {
    //             return;
    //         }
    //         $authService->getUserRow()->updateDayly($request);
    //     }
    // }

    public function bootstrapSession($e)
    {
        if($_SERVER['PHP_SELF'] != '/usr/bin/phpunit'){
            $memcache = $e->getApplication()
                ->getServiceManager()->get('Cache\Memcache');
            $saveHandler = new \Zend\Session\SaveHandler\Cache($memcache);
            $session = $e->getApplication()
                ->getServiceManager()
                ->get('Zend\Session\SessionManager');
            $session->setSaveHandler($saveHandler);
            if(!$e->getRequest() instanceof \Zend\Console\Request 
                and $token = $e->getRequest()->getQuery('access_token', false)
            ){
                $session->setId($token);
            }
            $session->start();

            $container = new Container('initialized');
            if (!isset($container->init)) {
                 $session->regenerateId(true);
                 $container->init = 1;
            }
        }
    }
    
    public function getConfig()
    {
        $config = include __DIR__ . '/config/module.config.php';
        $config['service_manager']['factories'] = array_merge($config['service_manager']['factories'],
            array(
                'Lib\Attribute\Table' => function($sm) {
                    return new Model\Lib\Attribute\Table($sm);
                },
                'Lib\Attribute\Option\Table' => function($sm) {
                    return new Model\Lib\Attribute\Option\Table($sm);
                },
                'Lib\Building\Table' => function($sm) {
                    return new Model\Lib\Building\Table($sm);
                },
                'Lib\Building\Level\Table' => function($sm) {
                    return new Model\Lib\Building\Level\Table($sm);
                },
                'Lib\Building\Level\Attribute\Table' => function($sm) {
                    return new Model\Lib\Building\Level\Attribute\Table($sm);
                },
                'Lib\Building\Level\Need\Table' => function($sm) {
                    return new Model\Lib\Building\Level\Need\Table($sm);
                },
                'Lib\Building\Level\Depend\Table' => function($sm) {
                    return new Model\Lib\Building\Level\Depend\Table($sm);
                },  
                'Lib\Building\Level\Production\Table' => function($sm) {
                    return new Model\Lib\Building\Level\Production\Table($sm);
                },  
                'Lib\Building\Level\Production\Need\Table' => function($sm) {
                    return new Model\Lib\Building\Level\Production\Need\Table($sm);
                },  
                'Lib\Resource\Table' => function($sm) {
                    return new Model\Lib\Resource\Table($sm);
                },
                'Lib\Resource\Inside\Table' => function($sm) {
                    return new Model\Lib\Resource\Inside\Table($sm);
                },
                'Lib\Ship\Table' => function($sm) {
                    return new Model\Lib\Ship\Table($sm);
                },
                'Lib\Ship\Attribute\Table' => function($sm) {
                    return new Model\Lib\Ship\Attribute\Table($sm);
                },
                'Lib\Ship\Need\Table' => function($sm) {
                    return new Model\Lib\Ship\Need\Table($sm);
                },
                'Lib\Ship\Slot\Table' => function($sm) {
                    return new Model\Lib\Ship\Slot\Table($sm);
                },
                'Lib\Ship\Weapon\Table' => function($sm) {
                    return new Model\Lib\Ship\Weapon\Table($sm);
                },
                'Lib\Skill\Table' => function($sm) {
                    return new Model\Lib\Skill\Table($sm);
                },
                'Lib\Skill\Level\Table' => function($sm) {
                    return new Model\Lib\Skill\Level\Table($sm);
                },
                'Lib\Skill\Level\Attribute\Table' => function($sm) {
                    return new Model\Lib\Skill\Level\Attribute\Table($sm);
                },
                'Lib\Skill\Level\Need\Table' => function($sm) {
                    return new Model\Lib\Skill\Level\Need\Table($sm);
                },
                'Lib\Skill\Level\Depend\Table' => function($sm) {
                    return new Model\Lib\Skill\Level\Depend\Table($sm);
                },
                'Lib\Starsystem\Table' => function($sm) {
                    return new Model\Lib\Starsystem\Table($sm);
                },
                'Lib\Starsystem\Gate\Table' => function($sm) {
                    return new Model\Lib\Starsystem\Gate\Table($sm);
                },
                'Lib\Starsystem\Planet\Table' => function($sm) {
                    return new Model\Lib\Starsystem\Planet\Table($sm);
                },
                'Lib\UserLevel\Table' => function($sm) {
                    return new Model\Lib\UserLevel\Table($sm);
                },
                'Lib\AllianceLevel\Table' => function($sm) {
                    return new Model\Lib\AllianceLevel\Table($sm);
                },
                'Lib\Unit\Table' => function($sm) {
                    return new Model\Lib\Unit\Table($sm);
                },
                'Lib\Unit\Attribute\Table' => function($sm) {
                    return new Model\Lib\Unit\Attribute\Table($sm);
                },
                'Lib\Unit\Need\Table' => function($sm) {
                    return new Model\Lib\Unit\Need\Table($sm);
                },
                'Lib\Unit\Depend\Table' => function($sm) {
                    return new Model\Lib\Unit\Depend\Table($sm);
                },
                'Lib\Npc\Table' => function($sm) {
                    return new Model\Lib\Npc\Table($sm);
                },
                'Lib\Npc\Guard\Table' => function($sm) {
                    return new Model\Lib\Npc\Guard\Table($sm);
                },
                'Lib\Npc\Level\Table' => function($sm) {
                    return new Model\Lib\Npc\Level\Table($sm);
                },
                'Lib\Npc\Level\Loot\Table' => function($sm) {
                    return new Model\Lib\Npc\Level\Loot\Table($sm);
                },
                'User\Table' => function($sm) {
                    return new Model\User\Table($sm);
                },
                'User\Uid\Table' => function($sm) {
                    return new Model\User\Uid\Table($sm);
                },
            )
        );
        return $config;
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
