<?php

namespace Ap\Cache\Storage\Adapter;

use Zend\Cache\Storage\Adapter\AdapterOptions;

class RedisOptions extends AdapterOptions
{
    protected $_host = '127.0.0.1';
    protected $_port = '6379';
    
    public function setHost($host)
    {
        $this->_host = $host;
        return $this;
    }
    
    public function getHost()
    {
        return $this->_host;
    }
    
    public function setPort($port)
    {
        $this->_port = $port;
        return $this;
    }
    
    public function getPort()
    {
        return $this->_port;
    }
    
    public function setPrefix($prefix)
    {
        $this->_prefix = $prefix;
        return $this;
    }
    
    public function getPrefix()
    {
        return $this->_prefix;
    }
}
