<?php

namespace Ap\Cache\Storage\Adapter;

use Zend\Cache\Storage\Adapter\AbstractAdapter AS AbstractAdapter;
use APCIterator as BaseApcIterator;
use stdClass;
use Traversable;
use Zend\Cache\Exception;
use Zend\Cache\Storage\AvailableSpaceCapableInterface;
use Zend\Cache\Storage\Capabilities;
use Zend\Cache\Storage\ClearByNamespaceInterface;
use Zend\Cache\Storage\ClearByPrefixInterface;
use Zend\Cache\Storage\FlushableInterface;
use Zend\Cache\Storage\IterableInterface;
use Zend\Cache\Storage\TotalSpaceCapableInterface;

class Redis extends AbstractAdapter implements FlushableInterface
{
    protected $_connect;
    public function __construct($options = null)
    {
        if (version_compare('2', phpversion('redis')) > 0) {
            throw new Exception\ExtensionNotLoadedException("Missing ext/redis >= 2");
        }

        parent::__construct($options);
    }

    public function getConnection()
    {
        if(null === $this->_connect){
            $this->_connect = new \Redis();
            $this->_connect->connect($this->getOptions()->getHost(), $this->getOptions()->port, 10);
            $this->_connect->setOption(\Redis::OPT_PREFIX, $this->getOptions()->getPrefix());
        }
        return $this->_connect;
    }

    public function setOptions($options)
    {
        return parent::setOptions(new RedisOptions($options));
    }

    public function getOptions()
    {
        if (!$this->options) {
            $this->setOptions(new RedisOptions());
        }
        return $this->options;
    }

    protected function internalGetItem(& $normalizedKey, & $success = null, & $casToken = null)
    {
        $str = $this->getConnection()->get($normalizedKey);
        if ($str) {
            list($data, $lifetime) = $this->_decode($str);
            return $data;
        }
        return null;
    }
    
    protected function internalSetItem(& $normalizedKey, & $value)
    {
        $str = $this->_encode(array($value, time()));
        return $this->getConnection()->set($normalizedKey, $str);
    }
    
    protected function internalRemoveItem(& $normalizedKey)
    {
        return $this->getConnection()->del($normalizedKey);
    }
    
    protected function _encode($data) {
        return serialize($data);
    }
    
    protected function _decode($data) {
        return unserialize($data);
    }
    
    public function lPush($normalizedKey, $value)
    {
        $str = $this->_encode(array($value, time()));
        $this->getConnection()->lPush($normalizedKey, $str);
        return $this;
    }
    
    public function lRange($normalizedKey, $index, $offset)
    {
        $data = array();
        foreach($this->getConnection()->lRange($normalizedKey, $index, $offset) AS $str){
            $data[] = $this->_decode($str);
        }
        return $data;
    }
    
    public function lLen($key)
    {
        return $this->getConnection()->lLen($key);
    }
    
    public function flush()
    {
        exec('redis-cli KEYS "'.$this->getOptions()->getPrefix().'_*" | xargs redis-cli DEL');
    }
}
