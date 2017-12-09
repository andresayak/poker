<?php

return array(
    'router' => array(
        'routes' => array(
            'api' => array(
                'type' => 'Segment',
                'options' => array(
                    'route' => '/api/:controller[.:action]',
                    'constraints' => array(
                        'controller' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                    ),
                    'defaults' => array(
                        '__NAMESPACE__' => 'Api\Controller',
                        'controller' => 'Index',
                        'action' => 'index',
                    ),
                ),
            ),
            'api-user-play' => array(
                'type' => 'Literal',
                'options' => array(
                    'route' => '/api/user.play',
                    'defaults' => array(
                        '__NAMESPACE__' => 'Api\Controller',
                        'controller' => 'Api\Controller\User',
                        'action' => 'play'
                    ),
                ),
            ),
            'api-user-login' => array(
                'type' => 'Literal',
                'options' => array(
                    'route' => '/api/user.login',
                    'defaults' => array(
                        '__NAMESPACE__' => 'Api\Controller',
                        'controller' => 'Api\Controller\User',
                        'action' => 'login'
                    ),
                ),
            ),
            'api-user-forgot' => array(
                'type' => 'Literal',
                'options' => array(
                    'route' => '/api/user.forgot',
                    'defaults' => array(
                        '__NAMESPACE__' => 'Api\Controller',
                        'controller' => 'Api\Controller\User',
                        'action' => 'forgot'
                    ),
                ),
            ),
            'api-user-signup' => array(
                'type' => 'Literal',
                'options' => array(
                    'route' => '/api/user.signup',
                    'defaults' => array(
                        '__NAMESPACE__' => 'Api\Controller',
                        'controller' => 'Api\Controller\User',
                        'action' => 'signup'
                    ),
                ),
            ),
            'api-main-cacheUpdate' => array(
                'type' => 'Literal',
                'options' => array(
                    'route' => '/api/main.cacheUpdate',
                    'defaults' => array(
                        '__NAMESPACE__' => 'Api\Controller',
                        'controller' => 'Api\Controller\Main',
                        'action' => 'cacheUpdate'
                    ),
                ),
            ),
            'api-main-cityUpdate' => array(
                'type' => 'Literal',
                'options' => array(
                    'route' => '/api/main.cityUpdate',
                    'defaults' => array(
                        '__NAMESPACE__' => 'Api\Controller',
                        'controller' => 'Api\Controller\Main',
                        'action' => 'cityUpdate'
                    ),
                ),
            ),
            'api-index-templates' => array(
                'type' => 'Literal',
                'options' => array(
                    'route' => '/api/index.templates',
                    'defaults' => array(
                        '__NAMESPACE__' => 'Api\Controller',
                        'controller' => 'Api\Controller\Index',
                        'action' => 'templates'
                    ),
                ),
            ),
            'access_token' => array(
                'type' => 'Literal',
                'options' => array(
                    'route' => '/access_token',
                    'defaults' => array(
                        '__NAMESPACE__' => 'Api\Controller',
                        'controller' => 'Api\Controller\Index',
                        'action' => 'access-token'
                    ),
                )
            ),
        ),
    ),
    'service_manager' => array(
        'factories' => array(
            'Slotmachine\Service'  =>  function($sm) {
                return new Api\Service\Slotmachine($sm);
            },
        ),
    ),
    'controllers' => array(
        'invokables' => array(
            'Api\Controller\Index'      => 'Api\Controller\IndexController',
            'Api\Controller\Poker'      => 'Api\Controller\PokerController',
            'Api\Controller\Shop'      => 'Api\Controller\ShopController',
            'Api\Controller\Chat'       => 'Api\Controller\ChatController',
            'Api\Controller\User'       => 'Api\Controller\UserController',
            'Api\Controller\Library'     => 'Api\Controller\LibraryController',
            'Api\Controller\Slotmachine'     => 'Api\Controller\SlotmachineController',
        ),
    ),
    'view_manager' => array(
        'template_path_stack' => array(
            __DIR__ . '/../view',
        ),
        'strategies' => array(
            'ViewJsonStrategy',
        ),
    ),
    'view_helpers' => array(
        'invokables'    =>  array(
        ),
    ),
    'acl' => array(
        'rules' => array(
            array(
                'role' => 'guest',
                'resource' => 'api',
                'permission' => 'allow'
            ),
        ),
        'resources' =>  array(
            'api' => array(
                'resource' => 'api',
            ),
            'access_token' => array(
                'resource' => 'access_token',
                'children' => array(
                    'api-main-error'    =>  array(
                        'resource'  =>  'api-main-error'
                    ),
                    'api-user-signup' => array(
                        'resource' => 'api-user-signup'
                    ),
                    'api-user-login' => array(
                        'resource' => 'api-user-login'
                    ),
                    'api-index-templates' => array(
                        'resource' => 'api-index-templates'
                    ),
                    'api-user-forgot' => array(
                        'resource' => 'api-user-forgot'
                    ),
                    'api-user-play' => array(
                        'resource' => 'api-user-play'
                    ),
                    'api-main-serverlist'   =>  array(
                        'resource' => 'api-main-serverlist'
                    ),
                    'api-main-cacheUpdate'  =>  array(
                        'resource'  =>  'api-main-cacheUpdate'
                    ),
                    'api-main-cityUpdate'   =>  array(
                        'resource'  =>  'api-main-cityUpdate'
                    ),
                    'api-user-forgot'   =>  array(
                        'resource'  =>  'api-user-forgot'
                    ),
                    'api-shop-callback' =>  array(
                        'resource'  =>  'api-shop-callback'
                    ),
                    'api-shop-getInfo' =>  array(
                        'resource'  =>  'api-shop-getInfo'
                    ),
                ),
            ),
        ),
    ),
);
