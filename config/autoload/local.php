<?php

return array(
    'server_code'   =>  'local',
    'testAuth'  =>  array(
        'userID'=> "1044266705603918"
    ),
    'multiServers'  =>  array(
        'dbmaster' => array(
            'driver'         => 'Pdo',
            'dsn'            => 'mysql:dbname=pokergame;host=localhost',
            'driver_options' => array(
                PDO::MYSQL_ATTR_INIT_COMMAND           =>  'SET NAMES \'UTF8\'',
                PDO::MYSQL_ATTR_USE_BUFFERED_QUERY     =>  true
            ),
            'username' => 'root',
            'password' => '123456',
        ),
        'dbslave' => array(
            'driver'         => 'Pdo',
            'dsn'            => 'mysql:dbname=pokergame;host=localhost',
            'driver_options' => array(
                PDO::MYSQL_ATTR_INIT_COMMAND           =>  'SET NAMES \'UTF8\'',
                PDO::MYSQL_ATTR_USE_BUFFERED_QUERY     =>  true
            ),
            'username' => 'root',
            'password' => '123456',
        ),
        'replicationTables' =>  array(
            '*' =>  true,
        ),
    ),
    'mongo' =>  array(
        'server'    =>  'mongodb://127.0.0.1:27017',
        'server_options'  =>  array(
            'connect'   =>  true
        )
    ),
    'facebook'  =>  array(
        'app_id'    =>  '1662255040703472',
        'key'       =>  '7757bbb49c51cdb28ceff9070a3dce61',
        'url'       =>  'https://apps.facebook.com/andresayak-poker'
    ),
    'memcache' =>  array(
        'namespace' =>  'globalamusepoker',
        'servers' => array('host' => '127.0.0.1')
    ),
    'redis' =>  array(
        'host'      =>  '127.0.0.1',
        'port'      =>  '6379',
        'prefix'    =>  'globalamusepoker_'
    ),
    'redisPoker' =>  array(
        'host'      =>  '127.0.0.1',
        'port'      =>  '6379',
        'prefix'    =>  'globalamusepokerPoker_'
    ),
    'redisChat' =>  array(
        'host'      =>  '127.0.0.1',
        'port'      =>  '6379',
        'prefix'    =>  'globalamusepokerChat_'
    ),
    'mongo' =>  array(
        'server'    =>  'mongodb://127.0.0.1:27017',
        'server_options'  =>  array(
            'connect'   =>  true
        )
    ),
    'constants' =>array(
        'SITE_NAME' =>  'GlobalAmuse Poker',
        'AUTH_TYPE' =>  'fb',
        'ERROR_MAILTO'  =>  false,
        'JSBUILD'   =>  false,
    ),
    'view_manager' => array(
        'display_not_found_reason' => true,
        'display_exceptions'       => true,
        'base_path' => 'https://pokergame.local/app/',
    ),
    'session' => array(
        'config' => array(
            'options'   =>  array(
                'cookie_domain'    =>  'pokergame.local',
            ),
        ),
    ),
);
