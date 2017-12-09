<?php

namespace Ap\MongoDb;

use MongoGridFS;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class GridFs implements FactoryInterface
{
    protected $prefix;
    protected $dbService;
    public function __construct($prefix, $dbService)
    {
        $this->prefix    = $prefix;
        $this->dbService = $dbService;
    }
    public function createService(ServiceLocatorInterface $services)
    {
        $db = $services->get($this->dbService);
        return new MongoGridFS($db, $this->prefix);
    }
}