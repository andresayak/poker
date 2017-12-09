<?php

return array(
    'console' => array(
        'router' => array(
            'routes' => array(
                'demon-run' => array(
                    'options' => array(
                        'route' => 'demon <mode> [<city_id>]',
                        'defaults' => array(
                            'controller' => 'Control\Controller\Demon',
                            'action' => 'run'
                        )
                    )
                ),
                
                'system-sync' => array(
                    'options' => array(
                        'route' => 'system sync',
                        'defaults' => array(
                            'controller' => 'Control\Controller\System',
                            'action' => 'sync'
                        )
                    )
                ),
                'system-monitoring' => array(
                    'options' => array(
                        'route' => 'system monitoring',
                        'defaults' => array(
                            'controller' => 'Control\Controller\System',
                            'action' => 'monitoring'
                        )
                    )
                ),
                'test' => array(
                    'options' => array(
                        'route' => 'test <action> <city_id>',
                        'defaults' => array(
                            'controller' => 'Control\Controller\Test',
                            'action' => 'index'
                        )
                    )
                ),
                'demon-process' => array(
                    'options' => array(
                        'route' => 'demon process',
                        'defaults' => array(
                            'controller' => 'Control\Controller\Demon',
                            'action' => 'process'
                        )
                    )
                ),
                'demon-workers' => array(
                    'options' => array(
                        'route' => 'demon workers',
                        'defaults' => array(
                            'controller' => 'Control\Controller\Demon',
                            'action' => 'workers'
                        )
                    )
                ),
                'demon-check' => array(
                    'options' => array(
                        'route' => 'demon check',
                        'defaults' => array(
                            'controller' => 'Control\Controller\Demon',
                            'action' => 'check'
                        )
                    )
                ),
                
                'demon-poker-run' => array(
                    'options' => array(
                        'route' => 'poker demon <mode>',
                        'defaults' => array(
                            'controller' => 'Control\Controller\Demon',
                            'action' => 'poker'
                        )
                    )
                ),
                'demon-run' => array(
                    'options' => array(
                        'route' => 'demon <mode> [<city_id>]',
                        'defaults' => array(
                            'controller' => 'Control\Controller\Demon',
                            'action' => 'run'
                        )
                    )
                ),
                'system-sync' => array(
                    'options' => array(
                        'route' => 'system sync',
                        'defaults' => array(
                            'controller' => 'Control\Controller\System',
                            'action' => 'sync'
                        )
                    )
                ),
                'poker-create' => array(
                    'options' => array(
                        'route' => 'poker create',
                        'defaults' => array(
                            'controller' => 'Control\Controller\Poker',
                            'action' => 'create'
                        )
                    )
                ),
                'poker-list' => array(
                    'options' => array(
                        'route' => 'poker list',
                        'defaults' => array(
                            'controller' => 'Control\Controller\Poker',
                            'action' => 'index'
                        )
                    )
                ),
                'poker-timeout' => array(
                    'options' => array(
                        'route' => 'poker timeout --id=',
                        'defaults' => array(
                            'controller' => 'Control\Controller\Poker',
                            'action' => 'timeout'
                        )
                    )
                ),
                
                'poker-start' => array(
                    'options' => array(
                        'route' => 'poker start --id=',
                        'defaults' => array(
                            'controller' => 'Control\Controller\Poker',
                            'action' => 'start'
                        )
                    )
                ),
                
                'poker-status' => array(
                    'options' => array(
                        'route' => 'poker status --id=',
                        'defaults' => array(
                            'controller' => 'Control\Controller\Poker',
                            'action' => 'status'
                        )
                    )
                ),
                
                'poker-info' => array(
                    'options' => array(
                        'route' => 'poker info --id=',
                        'defaults' => array(
                            'controller' => 'Control\Controller\Poker',
                            'action' => 'info'
                        )
                    )
                ),
                
                'poker-clear' => array(
                    'options' => array(
                        'route' => 'poker clear [--id=]',
                        'defaults' => array(
                            'controller' => 'Control\Controller\Poker',
                            'action' => 'clear'
                        )
                    )
                ),
                
                'poker-join' => array(
                    'options' => array(
                        'route' => 'poker join --id= --user_id= --position= --money=',
                        'defaults' => array(
                            'controller' => 'Control\Controller\Poker',
                            'action' => 'join'
                        )
                    )
                ),
                
                'poker-leave' => array(
                    'options' => array(
                        'route' => 'poker leave --id= --user_id=',
                        'defaults' => array(
                            'controller' => 'Control\Controller\Poker',
                            'action' => 'leave'
                        )
                    )
                ),
                
                'poker-check' => array(
                    'options' => array(
                        'route' => 'poker check --id= --user_id=',
                        'defaults' => array(
                            'controller' => 'Control\Controller\Poker',
                            'action' => 'check'
                        )
                    )
                ),
                
                'poker-allin' => array(
                    'options' => array(
                        'route' => 'poker allin --id= --user_id=',
                        'defaults' => array(
                            'controller' => 'Control\Controller\Poker',
                            'action' => 'allin'
                        )
                    )
                ),
                
                'poker-call' => array(
                    'options' => array(
                        'route' => 'poker call --id= --user_id=',
                        'defaults' => array(
                            'controller' => 'Control\Controller\Poker',
                            'action' => 'call'
                        )
                    )
                ),
                
                'poker-fold' => array(
                    'options' => array(
                        'route' => 'poker fold --id= --user_id=',
                        'defaults' => array(
                            'controller' => 'Control\Controller\Poker',
                            'action' => 'fold'
                        )
                    )
                ),
                
                'poker-raise' => array(
                    'options' => array(
                        'route' => 'poker raise --id= --user_id= --money=',
                        'defaults' => array(
                            'controller' => 'Control\Controller\Poker',
                            'action' => 'raise'
                        )
                    )
                ),
                
                'poker-test' => array(
                    'options' => array(
                        'route' => 'poker test <action>',
                        'defaults' => array(
                            'controller' => 'Control\Controller\Test',
                            'action' => '[0-9]'
                        )
                    )
                ),
                
                'system-log-rotate'=>array(
                    'options' => array(
                        'route' => 'log rotate',
                        'defaults' => array(
                            'controller' => 'Control\Controller\System',
                            'action'    => 'logRorate',
                        )
                    )
                ),
                'system-log-remove'=>array(
                    'options' => array(
                        'route' => 'system log remove',
                        'defaults' => array(
                            'controller' => 'Control\Controller\System',
                            'action'    => 'removeLog',
                        )
                    )
                ),
                'system-chat-reset'=>array(
                    'options' => array(
                        'route' => 'chat reset',
                        'defaults' => array(
                            'controller' => 'Control\Controller\System',
                            'action'    => 'chatReset',
                        )
                    )
                ),
                'system-cache-reset'=>array(
                    'options' => array(
                        'route' => 'cache reset <name>',
                        'defaults' => array(
                            'controller' => 'Control\Controller\System',
                            'action'    => 'cacheReset',
                        )
                    )
                ),
            )
        )
    ),
    'controllers' => array(
        'invokables' => array(
            'Control\Controller\Server' => 'Control\Controller\ServerController',
            'Control\Controller\User' => 'Control\Controller\UserController',
            'Control\Controller\Demon' => 'Control\Controller\DemonController',
            'Control\Controller\System' => 'Control\Controller\SystemController',
            'Control\Controller\Poker' => 'Control\Controller\PokerController',
        ),
    ),
    'view_manager' => array(
        'display_not_found_reason' => true,
        'display_exceptions' => true,
     )
);
