<?php

return array(
    'service_manager' => array(
        'factories' => array(
            'User\Table' => function($sm) {
                $table = new Game\Model\User\Table($sm);
                return $table;
            },
            'User\Object\Table' => function($sm) {
                $table = new Game\Model\User\Object\Table($sm);
                return $table;
            },
            'Poker\Table' => function($sm) {
                $table = new Game\Model\Poker\Table($sm);
                return $table;
            },
            'Shop\Table' => function($sm) {
                $table = new Game\Model\Shop\Table($sm);
                return $table;
            },
            'Shop\Object\Table' => function($sm) {
                $table = new Game\Model\Shop\Object\Table($sm);
                return $table;
            },
            'Lib\Object\Table' => function($sm) {
                $table = new Game\Model\Lib\Object\Table($sm);
                return $table;
            },
            'Lib\UserLevel\Table' => function($sm) {
                return new Game\Model\Lib\UserLevel\Table($sm);
            },
            'System\Paylog\Table' => function($sm) {
                return new Game\Model\System\Paylog\Table($sm);
            },
            'System\Server\Table' => function($sm) {
                return new Game\Model\System\Server\Table($sm);
            },
        )
    ),
    'controllers' => array(
        'invokables' => array(
            'Application\Controller\Index' => 'Application\Controller\IndexController',
        ),
    ),
);
        