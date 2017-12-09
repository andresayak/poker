<?php

namespace Ap;

use Ratchet\MessageComponentInterface;
use Ratchet\ConnectionInterface;

class SocketServer implements MessageComponentInterface 
{
    protected $clients, $options;
    
    public function __construct($options) 
    {
        $this->options = $options;
        $this->clients = new \SplObjectStorage;
    }
    
    public function getClients()
    {
        return $this->clients;
    }
    
    public function getCount()
    {
        return $this->clients->count();
    }
    
    public function onOpen(ConnectionInterface $conn) 
    {
        $cookies = $conn->WebSocket->request->getHeader('Cookie'); 
        $ip = $conn->WebSocket->request->getHeader('X-Real-IP');
        $ip = ($ip==null)?$conn->remoteAddress:$ip;
        if($cookies){
            $cookies = explode('; ', $cookies);
            foreach($cookies AS $cookie){
                if($cookie and preg_match('/PHPSESSID=([\w\d]+)/i', $cookie, $match)){
                    if($key = $match[1]){
                        $session_data = $this->options['memcache']->getItem($key);
                        if($session_data){
                            $return_data = array();
                            $offset = 0;
                            while ($offset < strlen($session_data)) {
                                if (!strstr(substr($session_data, $offset), "|")) {
                                    throw new Exception("invalid data, remaining: " . substr($session_data, $offset));
                                }
                                $pos = strpos($session_data, "|", $offset);
                                $num = $pos - $offset;
                                $varname = substr($session_data, $offset, $num);
                                $offset += $num + 1;
                                $data = unserialize(substr($session_data, $offset));
                                $return_data[$varname] = $data;
                                $offset += strlen(serialize($data));
                            }
                            if(isset($return_data['Zend_Auth']) 
                                and isset($return_data['Zend_Auth']->storage)
                            ){
                                $conn->user_id = (int)$return_data['Zend_Auth']->storage;
                                $this->clients->attach($conn);
                                echo "New connection! user_id = {$conn->user_id}, IP = ".$ip." \n";
                                $conn->send(json_encode(array(
                                    'auth' => true
                                )));
                                return ;
                            }
                        }
                    }
                }
            }
        }
        echo "Session invalid IP = ".$ip." cookies = ".print_r($cookies, true)."\n";
        $conn->send(json_encode(array(
            'auth' => true
        )));
        $this->saveStat();
        return ;
    }
    
    public function onMessage(ConnectionInterface $from, $msg) 
    {
        $numRecv = count($this->clients) - 1;
        echo sprintf('Connection %d sending message "%s" to %d other connection%s' . "\n"
            , $from->resourceId, $msg, $numRecv, $numRecv == 1 ? '' : 's');

        foreach ($this->clients as $client) {
            if ($from !== $client) {
                $client->send($msg);
            }
        }
    }

    public function onClose(ConnectionInterface $conn) 
    {
        $this->clients->detach($conn);

        $ip = $conn->WebSocket->request->getHeader('X-Real-IP');
        $ip = ($ip==null)?$conn->remoteAddress:$ip;
        echo "Connection user_id = {$conn->user_id}, IP = ".$ip." has disconnected\n";
    }

    public function onError(ConnectionInterface $conn, \Exception $e) 
    {
        echo "An error has occurred: {$e->getMessage()}\n";

        $conn->close();
    }
    
    public function saveStat()
    {
        if(isset($this->options['statfile'])){
            $memory_limit = ini_get('memory_limit');
            if (preg_match('/^(\d+)(.)$/', $memory_limit, $matches)) {
                if ($matches[2] == 'M') {
                    $memory_limit = $matches[1] * 1024 * 1024;
                } else if ($matches[2] == 'K') {
                    $memory_limit = $matches[1] * 1024;
                }
            }
            file_put_contents($this->options['statfile'], json_encode(array(
                'users' => $this->getCount(),
                'memory' => array(
                    'use' => memory_get_usage(true),
                    'all' => $memory_limit
                )
            )));
        }
    }
}