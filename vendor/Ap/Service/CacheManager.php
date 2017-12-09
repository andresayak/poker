<?php

namespace Ap\Service;

use Ap\Model\Table;

class CacheManager
{
    protected $_types, $_adapters;
    protected $_sm;
    protected $_geo = array();
    protected $_status = true;
    
    public function __construct($sm)
    {
        $this->_sm = $sm;
    }
    public function setStatus($status)
    {
        $this->_status = $status;
        return $this;
    }
    
    public function getStatus()
    {
        return $this->_status;
    }
    
    public function setTypes($types)
    {
        $this->_types = $types;
        return $this;
    }
    
    public function getTypes()
    {
        return $this->_types;
    }
    
    public function getSm()
    {
        return $this->_sm;
    }
    
    public function isCacheByTable(Table $table)
    {
        if($name = $this->getCacheNameByTable($table)){
            return $this->getAdapterByType($name);
        }
        return false;
    }
    
    public function getCacheNameByTable(Table $table)
    {
        $class = get_class($table);
        foreach($this->getTypes() AS $name=>$type){
            $status = false;
            if(isset($type['classes'])){
                if(in_array($class, $type['classes']))
                $status = true;
            }
            if(isset($type['path'])){
                foreach($type['path'] AS $path){
                    if(preg_match('/'.preg_quote($path,'/').'\\\\'.'/i', $class)){
                        $status = true;
                        break;
                    }
                }
            }
            if($status){
                if(!isset($type['adapter']))
                    throw new \Exception('Cache adapterName not set in CacheTypes');
                return $name;
            }
        }
        return false;
    }
    
    public function flushByType($type)
    {
        if($adapter = $this->getAdapterByType($type)){
            if($adapter instanceof \Zend\Cache\Storage\FlushableInterface){
                $adapter->flush();
                return true;
            }
        }
        return false;
    }
    
    public function getAdapterByType($type)
    {
        $cacheName = $this->_types[$type]['adapter'];
        if (!$adapter = $this->getSm()->get($cacheName)) {
            throw new \Exception('Cache adapter [' . $cacheName . '] not found');
        }
        return $adapter;
    }
}