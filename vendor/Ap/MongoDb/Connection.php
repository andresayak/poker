<?php

namespace Ap\MongoDb;

use Mongo;
use MongoClient;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class Connection implements FactoryInterface
{
    /**
     * Server connection string
     *
     * @var string
     */
    protected $server = 'mongodb://localhost:27017';
    /**
     * Connection options
     *
     * @var array
     */
    protected $options = array(
        'connect' => true
    );
    public function __construct($server = null, array $options = null)
    {
        if (null !== $server) {
            $this->server = $server;
        }
        if (null !== $options) {
            $this->options = $options;
        }
    }
    public function createService(ServiceLocatorInterface $services)
    {
        // Use MongoClient if available (ext/mongo >= 1.4)
        if (class_exists('MongoClient')) {
            return new MongoClient($this->server, $this->options);
        }
        return new Mongo($this->server, $this->options);
    }
}