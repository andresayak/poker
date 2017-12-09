<?php

namespace Ap\MongoDb;

class Entity{

    protected $_data = array();
    protected $_sm;
    protected $_collection;
    protected $_modifications = array(
        'inc'  =>  array(),
        'set'  =>  array(),
    );
    public function getId()
    {
        $data = $this->_data;
        return isset($data['_id'])?$data['_id']:false;
    }
    
    public function setSm($sm)
    {
        $this->_sm = $sm;
        return $this;
    }
    
    public function getSm()
    {
        return $this->_sm;
    }
    
    public function setCollection($collection)
    {
        $this->_collection = $collection;
        return $this;
    }
    
    public function getCollection()
    {
        return $this->_collection;
    }
    
    public function exchangeArray($data) 
    {
        if(isset($data['_id'])){
            $data['_id'] = (string)$data['_id']; 
        }
        $this->_data = $data;
        return $this;
    }

    public function toArray() 
    {
        $data = $this->_data;
        if(isset($data['_id'])){
            $data['id'] = (string)$data['_id']; 
            unset($data['_id']);
        }
        return $data;
    }
    
    public function inc($col, $value)
    {
        if(!isset($this->_data[$col])){
            $this->_data[$col] = 0;
        }
        $this->_data[$col]+= $col;
        $this->_modifications['inc'][$col] = $value;
    }
    
    public function __get($col)
    {
        $method_name = 'get'.str_replace(' ','', ucfirst(strtolower(str_replace('_',' ', $col))));
        if(method_exists($this, $method_name)){
            return $this->$method_name();
        }
        if(isset($this->_data[$col]))
            return $this->_data[$col];
    }

    public function __set($col, $value)
    {
        
        $method_name = 'set'.str_replace(' ','', ucfirst(strtolower(str_replace('_',' ', $col))));
        if(method_exists($this, $method_name)){
            return $this->$method_name($value);
        }
        if(!isset($this->_data[$col])){
            $this->_data[$col] = null;
        }
        
        if($this->_data[$col] !== $value){
            $this->_modifications['set'][$col] = $this->_data[$col];
            $this->_data[$col] = $value;
        }
    }
    
    public function __isset($columnName)
    {
        return array_key_exists($columnName, $this->_data);
    }
    
    public function toArrayForSave()
    {
        $data = array();
        if(count($this->_modifications['inc'])){
            $data['$inc'] = $this->_modifications['inc'];
        }
        
        if(count($this->_modifications['set'])){
            $data['$set'] = array();
            foreach($this->_modifications['set'] AS $col=>$value){
                $data['$set'][$col]= $this->$col;
            }
        }
        return $data;
    }
    
    public function clearModifications()
    {
        $this->_modifications = array(
            '$set'  =>  array(),
            '$inc'  =>  array()
        );
        return $this;
    }
    
    public function save()
    {
        $this->getCollection()->update($this);
    }
    
    public function remove()
    {
        $this->getCollection()->remove($this);
    }
}
