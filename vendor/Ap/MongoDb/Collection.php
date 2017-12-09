<?php

namespace Ap\MongoDb;

use MongoCollection;

class Collection
{
    protected $name = 'dialogs';
    protected $collection;
    protected $_sm;
    protected $_depends = array();
    protected $_defauls = array();
    protected $_entitySetClass = 'EntitySet';
    protected $_entityClass = 'Entity';
    protected $_entitySetPrototype;
    protected $_datatypes = array();
    
    public function getEntitySetPrototype()
    {
        if($this->_entitySetPrototype === null){
            $this->_entitySetPrototype = $this->createEntitySetPrototype();
        }
        return $this->_entitySetPrototype;
    }
    
    public function setEntitySetPrototype(EntitySet $entity)
    {
        $this->_entitySetPrototype = $entity;
        return $this;
    }
    
    protected function createEntitySetPrototype()
    {
        $path = explode('\\', get_class($this));
        array_pop($path);
        $baseClass = implode('\\',$path);
        $entitySetClass =  $baseClass.'\\'.$this->_entitySetClass;
        if(!class_exists($entitySetClass))
            $entitySetClass = __NAMESPACE__.'\\'.$this->_entitySetClass;
        $entitySetPrototype = new $entitySetClass;
        $entitySetPrototype->setCollection($this);
        $entitySetPrototype->setSm($this->getSm());
        
        $entityPrototype = $this->createEntityPrototype();
        return $entitySetPrototype->setEntityPrototype($entityPrototype);
    }
    
    protected function createEntityPrototype()
    {
        $path = explode('\\', get_class($this));
        array_pop($path);
        $baseClass = implode('\\',$path);
        $entityClass =  $baseClass.'\\'.$this->_entityClass;
        if(!class_exists($entityClass))
            $entityClass = __NAMESPACE__.'\\'.$this->_entityClass;
        $entityPrototype = new $entityClass;
        $entityPrototype->setCollection($this);
        $entityPrototype->setSm($this->getSm());
        $entityPrototype->exchangeArray($this->_defauls);
        return $entityPrototype;
    }
    
    public function __construct($service)
    {
        $this->service = $service;
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
    
    public function getCollection()
    {
        if($this->collection === null){
            $this->collection = new MongoCollection($this->service, $this->name);
        }
        return $this->collection;
    }
    
    public function createEntity($data){
        
        $entity = clone $this->createEntityPrototype();
        $entity->exchangeArray($data);
        return $entity;
    }

    public function findBy($options = array())
    {
        return $this->getCollection()->find($options);
    }
    
    public function findAllBy($options = array())
    {
        $cursor = $this->findBy($options);
        $result = array();
        foreach ($cursor as $doc) {
            $result[] = $doc;
        }
        $entitySet = clone $this->createEntitySetPrototype();
        return $entitySet->exchangeArray($result);
    }
    
    public function findOneBy($options = array())
    {
        $result = $this->getCollection()->findOne($options);
        if($result!==null){
            $entity = clone $this->createEntityPrototype();
            return $entity->exchangeArray($result);
        }
        return false;
    }
    
    public function findById($id)
    {
        try {
            $mongoId = new \MongoId($id);
        } catch (\MongoException $exc) {
            return false;
        }

        $data = $this->getCollection()->findOne(array(
            '_id'   =>  $mongoId
        ));
        if($data === null){
            return false;
        }
        $entity = clone $this->createEntityPrototype();
        $entity->exchangeArray($data);
        return $entity;
    }
    
    public function save(Entity $entity)
    {
        if($entity->getId()){
            return $this->update($entity);
        }
        return $this->getCollection()->insert($entity->toArray());
    }
    
    public function update(Entity $entity)
    {
        $data = $entity->toArrayForSave();
        $mongoId = new \MongoId($entity->getId());
        $result =  $this->getCollection()->update(
            array("_id" => $mongoId), 
            $data, 
            array("multiple" => true));
        
        if($result){
            $this->clearModifications();
        }
        return $result;
    }
    
    public function remove(Entity $entity)
    {
        $mongoId = new \MongoId($entity->getId());
        $this->removeBy(array('_id' => $mongoId));
    }
    
    
    public function removeBy($options = array())
    {
        return $this->getCollection()->remove($options);
    }
}

