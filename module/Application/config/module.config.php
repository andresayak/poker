<?php

return array(
    'router' => array(
        'routes' => array(
            'default' => array(
                'type' => 'Segment',
                'options' => array(
                    'route' => '/[:controller[/:action]]',
                    'constraints' => array(
                        'controller' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                   ),
                    'defaults' => array(
                        '__NAMESPACE__' => 'Application\Controller',
                        'controller' => 'Index',
                        'action' => 'index',
                    ),
                ),
            ),
            
            'app' => array(
                'type' => 'Literal',
                'options' => array(
                    'route' => '/app/',
                    'defaults' => array(
                        'controller' => 'Application\Controller\Index',
                        'action' => 'app',
                    ),
                ),
            ),
            'signup_confirm' => array(
                'type' => 'Segment',
                'options' => array(
                    'route' => '/confirm/:user_id/:key',
                    'defaults' => array(
                        'controller' => 'Application\Controller\Auth',
                        'action' => 'confirm',
                    ),
                ),
            ),
            'forgot_newpass' => array(
                'type' => 'Segment',
                'options' => array(
                    'route' => '/newpass/:user_id/:key',
                    'defaults' => array(
                        'controller' => 'Application\Controller\Auth',
                        'action' => 'newpass',
                    ),
                ),
            ),
        ),
    ),
    'service_manager' => array(
        'factories' => array(
            'Zend\Session\SessionManager' => function ($sm) {
                $config = $sm->get('config');
                if (isset($config['session'])) {
                    $session = $config['session'];

                    $sessionConfig = null;
                    if (isset($session['config'])) {
                        $class = isset($session['config']['class']) ? $session['config']['class'] : 'Zend\Session\Config\SessionConfig';
                        $options = isset($session['config']['options']) ? $session['config']['options'] : array();
                        $sessionConfig = new $class();
                        $sessionConfig->setOptions($options);
                    }

                    $sessionStorage = null;
                    if (isset($session['storage'])) {
                        $class = $session['storage'];
                        $sessionStorage = new $class();
                    }

                    $sessionSaveHandler = null;
                    if (isset($session['save_handler'])) {
                        $sessionSaveHandler = new \Zend\Session\SaveHandler\Cache($sm->get($session['save_handler']));
                    }

                    $sessionManager = new \Zend\Session\SessionManager($sessionConfig, $sessionStorage, $sessionSaveHandler);

                    if (isset($session['validators'])) {
                        $chain = $sessionManager->getValidatorChain();
                        foreach ($session['validators'] as $validator) {
                            $validator = new $validator();
                            $chain->attach('session.validate', array($validator, 'isValid'));
                        }
                    }
                } else {
                    $sessionManager = new SessionManager();
                }
                \Zend\Session\Container::setDefaultManager($sessionManager);
                return $sessionManager;
            },
            'CacheManager'  =>  function($sm) {
                $config = $sm->get('config');
                $service = new Ap\Service\CacheManager($sm);
                if(isset($config['cacheType']))
                    $service->setTypes($config['cacheType']);
                return $service;
            },
            'Cache\Redis' => function($sm) {
                $config = $sm->get('Config');
                return Zend\Cache\StorageFactory::factory(array(
                    'adapter' => array(
                        'name'      =>  '\Ap\Cache\Storage\Adapter\Redis',
                        'options'   =>  $config['redis']
                     ),
                     'plugins' => array(
                        'IgnoreUserAbort' => array(
                            'exitOnAbort' => true
                         ),
                     )
                ));
            },
            'Chat\Cache\Storage' => function($sm) {
                $config = $sm->get('Config');
                return Zend\Cache\StorageFactory::factory(array(
                    'adapter' => array(
                        'name'      =>  '\Ap\Cache\Storage\Adapter\Redis',
                        'options'   =>  $config['redisChat']
                     ),
                     'plugins' => array(
                        'IgnoreUserAbort' => array(
                            'exitOnAbort' => true
                         ),
                     )
                ));
            },
            'Mailer' => function($sm) {
                $config = $sm->get('Config');
                $mailer = new Ap\Service\Mailer($config['mailer']);
                $mailer->setRenderer($sm->get('ViewRenderer'));
                return $mailer;
            },
            'Application\Service\ErrorHandling' => function($sm) {
                return new \Application\Service\ErrorHandling($sm->get('Zend\Log'));
            },
            'Pay\Log' => function ($sm) {
                $date = date('Y-m-d');
                $logger = new \Zend\Log\Logger;
                $writer = new \Zend\Log\Writer\Stream(__DIR__.'/../../../data/logs/pays_' . $date . '.txt');
                $logger->addWriter($writer);
                return $logger;
            },
            'Zend\Log' => function ($sm) {
                $date = date('Y-m-d');
                $logger = new \Zend\Log\Logger;
                $writer = new \Zend\Log\Writer\Stream(__DIR__.'/../../../data/logs/error_' . $date . '.txt');
                $writer->addFilter(new Zend\Log\Filter\Priority(\Zend\Log\Logger::ERR));
                $logger->addWriter($writer);
                return $logger;
                
            },
            'Mail\Log' =>  function($sm){
                $config = $sm->get('config');
                $mail = new \Zend\Mail\Message();
                $mail->setFrom($config['mailer']['site_email'])
                    ->addTo(ERROR_MAILTO)
                    ->setSubject('Log report '.date('Y-m-d').' from server '.SERVER_ID);
                $logger = new \Zend\Log\Logger;
                $logger->addWriter(new \Zend\Log\Writer\Mail($mail, new \Zend\Mail\Transport\Sendmail()));
                return $logger;
             },
            'Transaction' => function($sm) {
                return new \Ap\Transaction($sm);
            },
            'translator' => function($sm){ 
                $config = $sm->get('Config');
                return Ap\I18n\Translator\Translator::factory($config['translator']);
            },
            'Auth\Service' => function($sm) {
                $service = new Application\Service\Auth($sm->get('User\Table'));
                $service->setSm($sm);
                return $service;
            },
            'Acl\Service' => function($sm) {
                try {
                    $config = $sm->get('config');
                    $service = new Ap\Service\Acl();
                    $service->setRoles($config['acl']['roles'])
                        ->setRules($config['acl']['rules'])
                        ->initResources($config['acl']['resources']);
                } catch (\Exception $exc) {
                    echo $exc;exit;
                }
                return $service;
            },
            'Cache\Memcache' => function($sm) {
                $config = $sm->get('Config');
                $cache = Zend\Cache\StorageFactory::factory(array(
                    'adapter' => 'memcache',
                    'plugins' => array(
                        'exception_handler' => array('throw_exceptions' => true),
                        'serializer'
                    )
                ));
                $cache->setOptions($config['memcache']);
                return $cache;
            },
            'PushCommet\Service' => function($sm) {
                return new Application\Service\PushCommet($sm);
            },
            'Poker\Cache\Storage' => function($sm) {
                $config = $sm->get('Config');
                return Zend\Cache\StorageFactory::factory(array(
                    'adapter' => array(
                        'name'      =>  '\Ap\Cache\Storage\Adapter\Redis',
                        'options'   =>  $config['redisPoker']
                     ),
                     'plugins' => array(
                        'IgnoreUserAbort' => array(
                            'exitOnAbort' => true
                         ),
                     )
                ));
            },
            'System\Feedback\Table' =>  function($sm){
                return new Application\Model\System\Feedback\Table($sm);
            },
            'Chat\Service'  =>  function($sm) {
                return new Application\Service\Chat($sm);
            },
            'Mongo'         =>  function($sm){
                $config = $sm->get('config');
                $config = $config['mongo'];
                $factory = new Ap\MongoDb\Connection($config['server'], $config['server_options']);
                return $factory->createService($sm);
            },
            'MongoDB'   => new Ap\MongoDb\Db\Factory('anotherspace', 'Mongo'),
            'Chat\Collection'   => function($sm){
                $collection = new Application\Model\Chat\Collection($sm->get('MongoDB'));
                $collection->setSm($sm);
                return $collection;
            },
            'Zend\Db\Adapter\Adapter'   =>  function($sm){
                return $sm->get('MultiServers\Service')->createService();
            },
            'MultiServers\Service'  =>  function($sm){
                $config = $sm->get('config');
                return new Ap\Service\MultiServers($sm, $config['multiServers']);
            }
        ),
    ),
    'translator' => array(
        'locale' => 'en',
        'translation_file_patterns' => array(
            array(
                'type' => 'phparray',
                'base_dir' => __DIR__ . '/../language',
                'pattern' => '%s.php',
            ),
        ),
    ),
    'controllers' => array(
        'invokables' => array(
            'Application\Controller\Index' => 'Application\Controller\IndexController',
        ),
    ),
    'view_manager' => array(
        'display_not_found_reason' => true,
        'display_exceptions' => true,
        'doctype' => 'HTML5',
        'not_found_template' => 'error/404',
        'exception_template' => 'error/index',
        'template_map' => array(
            'layout/layout' => __DIR__ . '/../view/layout/layout.phtml',
            'layout/app' => __DIR__ . '/../view/layout/app.phtml',
            'application/index/index' => __DIR__ . '/../view/application/index/index.phtml',
            'error/404' => __DIR__ . '/../view/error/404.phtml',
            'error/index' => __DIR__ . '/../view/error/index.phtml',
        ),
        'template_path_stack' => array(
            __DIR__ . '/../view',
        ),
    ),
    'view_helpers' => array(
        'invokables' => array(
            'notification' => 'Application\View\Helper\Notification',
            'needBuildList' => 'Application\View\Helper\NeedBuildList',
            'needResourceList' => 'Application\View\Helper\NeedResourceList',
            'dateMessage' => 'Application\View\Helper\DateMessage',
            'dateNotification' => 'Application\View\Helper\DateNotification',
            'givesList' => 'Application\View\Helper\GivesList',
            'attrValueBlock' => 'Application\View\Helper\AttrValueBlock',
        ),
        'factories' => array(
            'baseDynamicPath' => function($sm) {
                return new Application\View\Helper\BaseDynamicPath($sm);
            },
            'baseStaticPath' => function($sm) {
                $config = $sm->getServiceLocator()->get('config');
                return new Application\View\Helper\BaseStaticPath($config['static_host']);
            },
            'sm' => function($sm) {
                return new Application\View\Helper\Sm($sm->getServiceLocator());
            },
            'auth' => function($sm) {
                $authService = $sm->getServiceLocator()->get('Auth\Service');
                $helper = new Application\View\Helper\Auth($authService);
                return $helper;
            },
            'acl' => function($sm) {
                $helper = new Application\View\Helper\Acl($sm->getServiceLocator());
                return $helper;
            },
            'flashMessages' => function($sm) {
                $flashmessenger = $sm->getServiceLocator()
                    ->get('ControllerPluginManager')
                    ->get('flashmessenger');
                $helper = new Application\View\Helper\FlashMessages($flashmessenger);
                return $helper;
            }
        )
    ),
    'acl' => array(
        'resources'=>array(
            'default' => array(
                'resource' => 'default',
                'children' => array(
                    'index' => array(
                        'resource' => 'application-index-index',
                    ),
                    'app' => array(
                        'resource' => 'application-index-app',
                    ),
                    'login' => array(
                        'resource' => 'login',
                    ),
                    'forgot_newpass' => array(
                        'resource'  =>  'forgot_newpass'
                    ),
                    'signup_confirm'    =>  array(
                        'resource'  =>  'signup_confirm'
                    ),
                    'api-main-getLibrary'   =>  array(
                        'resource'  =>  'api-main-getLibrary'
                    )
                ),
            ),
        ),
    ),
);
