<?php

namespace Ap\MongoDb\Collection;

use MongoCollection;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class Factory implements FactoryInterface
{
    protected $collectionName;
    protected $dbService;
    
    public function __construct($collectionName, $dbService)
    {
        $this->collectionName    = $collectionName;
        $this->dbService         = $dbService;
    }
    
    public function createService(ServiceLocatorInterface $services)
    {
        $db = $services->get($this->dbService);
        return new MongoCollection($db, $this->collectionName);
    }
}