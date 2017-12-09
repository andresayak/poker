<?php

return array(
    'server_code'   =>  false,
    'facebook'  =>  array(
        'app_id'    =>  '1660956647499978',
        'key'       =>  '7438a0f4d5337e83dd86bb003d4cfbb1',
        'ver'   =>      'v2.5',
        'url'       =>  'https://apps.facebook.com/globalamuse-poker'
    ),
    'multiServers'  =>  array(
        'dbmaster' => array(
            'driver'         => 'Pdo',
            'dsn'            => 'mysql:dbname=poker;host=localhost',
            'driver_options' => array(
                PDO::MYSQL_ATTR_INIT_COMMAND           =>  'SET NAMES \'UTF8\'',
                PDO::MYSQL_ATTR_USE_BUFFERED_QUERY     =>  true
            ),
            'username' => 'root',
            'password' => '123456',
        ),
        'dbslave' => array(
            'driver'         => 'Pdo',
            'dsn'            => 'mysql:dbname=poker;host=localhost',
            'driver_options' => array(
                PDO::MYSQL_ATTR_INIT_COMMAND           =>  'SET NAMES \'UTF8\'',
                PDO::MYSQL_ATTR_USE_BUFFERED_QUERY     =>  true
            ),
            'username' => 'root',
            'password' => '123456',
        ),
        'replicationTables' =>  array(
            '*'=>   true
        ),
    ),
    'workStatus'=>array(
        'status'    =>  false,
        'workStart' =>  strtotime('6 October 2015'),
        'workEnd' =>  strtotime('7 October 2015')
    ),
    'developers_ips' =>  array(
        '::1',
        '127.0.0.1',
    ),
    'socket' => array(
        'period'    =>  0.3,
        'file' => '/tmp/apocalypse_socket_server.pid',
        'statfile'  =>  '/tmp/socket_stat'
    ),
    'spam'  =>  array(
        'types' =>  array(
            'api'   =>  array(
                'limits' => array(
                    'hour'  =>  array(
                        'limit'     =>  1500,
                        'period'    =>  3600,
                        'block'     =>  3600,
                    ),
                    'day'  =>  array(
                        'limit'     =>  5000,
                        'period'    =>  86400,
                        'block'     =>  86400,
                    )
                ),
            )
        )
    ),
    'mongo' =>  array(
        'server'    =>  'mongodb://192.168.3.1:27017',
        'server_options'  =>  array(
            'connect'   =>  true
        )
    ),
    'view_manager' => array(
        'headers' => array(
            'Access-Control-Allow-Origin' => 'https://poker.royal-wars.com',
            'Access-Control-Allow-Credentials' => 'true',
            'Access-Control-Allow-Methods' => 'GET, POST, OPTIONS',
            'Access-Control-Allow-Headers' => 'DNT,X-CustomHeader,Keep-Alive,User-Agent,X-Requested-With,If-Modified-Since,Cache-Control,Content-Type'
        ),
        'display_not_found_reason' => false,
        'display_exceptions'       => false,
        'base_path' => 'http://poker.royal-wars.com/app/',
    ),
    'langs' =>  array(
        'availables' => array(
            'en', 'fr', 'de', 'ru', 'es', 'it'
        ),
        'default'   =>  'en'
    ),
    'mailer'   =>  array(
        'status'    =>  true,
        'site_email'  =>  'robot@globalamuse.com',
        'site_name' =>  'GlobalAnuse Poker',
        'feedback_emails'   =>  array(
            'andresayak@gmail.com',
        ),
        'errors'    =>  array(
            'andresayak@gmail.com'
        ),
        'smtp'  =>  array(
            'name'              =>  'mandril',
            'host'              =>  'smtp.mandrillapp.com',
            'connection_class'  =>  'plain',
            'port'              =>  587,
            'connection_config' => array(
                'username'  =>  'andresayak@gmail.com',
                'password'  =>  '91ejFamrxMlahEYCz-Yzzw',
                'ssl'       => 'tls',
            )
        )
    ),
    'session' => array(
        'config' => array(
            'class' => 'Zend\Session\Config\SessionConfig',
            'options'   =>  array(
                'use_cookies' => true,
                'cookie_domain'    =>  '.royal-wars.com',
            ),
        ),
        'storage' => 'Zend\Session\Storage\SessionArrayStorage',
        'validators' => array(
            'Zend\Session\Validator\RemoteAddr',
            'Zend\Session\Validator\HttpUserAgent',
        ),
        'save_handler'  =>  'Cache\Memcache'
    ),
    'constants' =>  array(
        'JSBUILD'   =>  false,
        'APP_VERSION'   =>  '0',
        'AUTH_TYPE' =>  'default',
        'ERROR_MAILTO'  =>  'andresayak@gmail.com',
    ),
    'phpSettings'=>array(
        'memory_limit'                  =>  '256M',
        'display_startup_errors'        =>  0,
        'display_errors'                =>  0,
        'date.timezone'                 =>  'UTC',
        //'max_execution_time'            =>  180,
    ),
    'cacheType' =>  array(
        'library' =>    array(
            'path'      =>  array(
                'Game\Model\Lib'
            ),
            'adapter'   =>  'Cache\Redis',
            'lifetime'  =>  2419200
         ),
    ),
    'log'   =>  './data/logs/',
    'memcache' =>  array(
        'namespace' =>  'globalamusepoker',
        'servers' => array('host' => '192.168.3.1')
    ),
    'redis' =>  array(
        'host'      =>  '127.0.0.1',
        'port'      =>  '6379',
        'prefix'    =>  'globalamusepoker_'
    ),
    'redisChat' =>  array(
        'host'      =>  '192.168.3.1',
        'port'      =>  '6379',
        'prefix'    =>  'globalamusepokerChat_'
    ),
    'redisPoker' =>  array(
        'host'      =>  '192.168.3.1',
        'port'      =>  '6379',
        'prefix'    =>  'globalamusepokerPoker_'
    ),
    'managing_lock_file'    =>  '/tmp/globalamusepoker-demon.pid',
    'acl'   =>  array(
        'roles' => array(
            array(
                'code'      =>  'guest',
                'parent'    =>  null,
                'priority'  =>  0
            ),
            array(
                'code'      =>  'user',
                'parent'    =>  'guest',
                'priority'  =>  10
            ),
            array(
                'code'      =>  'tester',
                'parent'    =>  'user',
                'priority'  =>  20
            ),
            array(
                'code'      =>  'moderator',
                'parent'    =>  'user',
                'priority'  =>  30
            ),
            array(
                'code'      =>  'admin',
                'parent'    =>  'user',
                'priority'  =>  40
            ),
         ),
        'rules' =>  array(
            array(
                'role'          =>  'guest',
                'resource'      =>  'default',
                'permission'    =>  'allow'
            ),
            array(
                'role'          =>  'user',
                'resource'      =>  'api',
                'permission'    =>  'allow'
            ),
            array(
                'role'          =>  'guest',
                'resource'      =>  'access_token',
                'permission'    =>  'allow'
            ),
            array(
                'role'          =>  'guest',
                'resource'      =>  'api-user-play',
                'permission'    =>  'allow'
            ),
            array(
                'role'          =>  'guest',
                'resource'      =>  'api-shop-callback',
                'permission'    =>  'allow'
            ),
            array(
                'role'          =>  'guest',
                'resource'      =>  'application-index-app',
                'permission'    =>  'allow'
            )
        )
    ),
    'templates' => array(
    ),
    'ip_bans'  =>  array(
    )
);
