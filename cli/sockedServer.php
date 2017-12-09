<?php

ini_set('display_startup_errors', 1);
ini_set('display_errors', 1);
ini_set('error_reporting', E_ALL & ~E_DEPRECATED);
date_default_timezone_set('UTC');

define('INDEX_PATH', realpath(dirname(__FILE__) . '/../'));
chdir(INDEX_PATH);

$localconfig = include 'config/autoload/local.php';
$globalconfig = include 'config/autoload/global.php';
$config = array_replace_recursive($globalconfig, $localconfig);

$loader = include 'vendor/autoload.php';
$loader->add('Ap', 'vendor');

use Ratchet\Server\IoServer;
use Ratchet\Http\HttpServer;
use Ap\SocketServer;
use Ratchet\WebSocket\WsServer;
use React\Socket\Connection;
use React\Socket\ConnectionInterface;

require 'vendor/autoload.php';
$pid_file_name = $config['socket']['file'];

function shutdown() {
    global $pid_file_name;
    if (is_file($pid_file_name))
        unlink($pid_file_name);
    $error = error_get_last();
    if ($error !== null) {
        $errno = $error["type"];
        $errfile = $error["file"];
        $errline = $error["line"];
        $errstr = $error["message"];
        $message = print_r($error, true);
        $hash = md5($message . date('Y-m-d-H'));
        $file = 'data/tmp/socked_' . date('Y-m-d-H') . '_' . $hash;
        if (!is_file($file)) {
            file_put_contents($file, $message);
        }
    }
}

function exceptions_error_handler($severity, $message, $filename, $lineno) {
    if (error_reporting() == 0) {
        return;
    }
    if (error_reporting() & $severity) {
        throw new \ErrorException($message, 0, $severity, $filename, $lineno);
    }
}

set_error_handler('exceptions_error_handler');
register_shutdown_function('shutdown');

//try {
    if (is_file($pid_file_name)) {
        $pid = file_get_contents($pid_file_name);
        if (function_exists('posix_kill') && posix_kill($pid, 0)) {
            exit;
        } else {
            echo 'unlink old pid file' . "\n";
            unlink($pid_file_name);
        }
    }
    $child_pid = getmypid();
    file_put_contents($pid_file_name, $child_pid);

    function echo_memory_usage() {
        $mem_usage = memory_get_usage(true);
        if ($mem_usage < 1024)
            return $mem_usage . " b";
        elseif ($mem_usage < 1048576)
            return round($mem_usage / 1024, 2) . " Kb";
        else
            return round($mem_usage / 1048576, 2) . " Mb";
    }

    if (!isset($config['redis'])) {
        throw new \Exception('redis config not set');
    }
    $redis = new Redis();
    $redis->connect($config['redisChat']['host'], $config['redisChat']['port']);
    $redis->setOption(Redis::OPT_PREFIX, $config['redisChat']['prefix']);

    $redisPoker = new Redis();
    $redisPoker->connect($config['redisPoker']['host'], $config['redisPoker']['port']);
    $redisPoker->setOption(Redis::OPT_PREFIX, $config['redisPoker']['prefix']);
    
    $memcached = Zend\Cache\StorageFactory::factory(array(
        'adapter' => 'Memcache',
        'plugins' => array(
           'exception_handler' => array('throw_exceptions' => false),
           'serializer'
        )
    ));
    $memcached->setOptions($config['memcache']);

    //$db = new \Zend\Db\Adapter\Adapter($config['multiServers']['dbmaster']);
    $socketServer = new SocketServer(array(
        'statfile' => $config['socket']['statfile'],
        'memcache' => $memcached,
            //'db' => $db
    ));
    $server = IoServer::factory(
        new HttpServer(new WsServer($socketServer)), 8010
    );
    echo 'Start' . "\n";
    echo 'redisPoker ' . $config['redis']['host'] . "\n";
    echo 'memcache ' . print_r($config['memcache']['servers'], true) . "\n";

    $server->loop->addPeriodicTimer($config['socket']['period'], function() use ($socketServer, $redis, $redisPoker, $config) {
        $messages = array(
        );
        //try {
            while ($json = $redis->lPop('socketServer')) {
                var_dump($json);
                if (!$socketServer->getCount()) {
                    continue;
                }
                $data = json_decode($json);
                if ($data->channel == 'room' || $data->channel == 'private') {
                    foreach($data->messages AS $message){
                        $room_id = $message->room_id;
                        if (!isset($messages[$room_id])) {
                            $messages[$room_id] = array(
                                'private' => array(),
                                'room' => array(),
                            );
                        }
                        if ($data->channel == 'room') {
                            $messages[$room_id]['room'][] = $message;
                        } elseif ($data->channel == 'private') {
                            $messages[$room_id]['private'][] = $message;
                        }
                    }
                }
            }
            if (count($messages)) {
                $logs = '';
                $logs.= '--------------- date: ' . date('m.d.y H:i:s') . " ------------\n";
                foreach ($messages AS $room_id => $messageList) {
                    $user_ids = $redisPoker->SMEMBERS('game_poker_players' . $room_id . '_listens');
                    foreach ($messageList AS $type => $items) {
                        if (count($items)) {
                            $logs.=' - - send to '.$type.' channel ' . $room_id . "\n";
                            $json = json_encode(array(
                                'channel' => $type,
                                'room_id' => $room_id,
                                'messages' => $items
                            ));
                            foreach ($user_ids AS $user_id) {
                                foreach ($socketServer->getClients() AS $client) {
                                    if ((int) $client->user_id == (int) $user_id) {
                                        $logs.=' + ' . $client->user_id . "\n";
                                        $client->send($json);
                                    }
                                }
                            }
                        }
                    }
                }
            }
            if (isset($logs)) {
                $logs.="\n";
                echo $logs;
                unset($logs);
            }
            $socketServer->saveStat();
        //} catch (\ErrorException $exc) {
        //    echo $exc->getTraceAsString();
        //}
    });
    $server->run();
/*} catch (\React\Socket\ConnectionException $e) {
    
} catch (\Exception $e) {
    $message = "Exception:\n" . $e->__toString();
    $message.= "\nTrace:\n" . $e->getTraceAsString();
    echo $message;
    $hash = md5($message . date('Y-m-d-H'));
    $file = dirname(INDEX_PATH) . '/data/tmp/socked_' . date('Y-m-d-H') . '_' . $hash;
    if (!is_file($file)) {
        file_put_contents($file, $message);
    }
    shutdown();
}
*/