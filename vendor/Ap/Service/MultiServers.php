<?php

namespace Ap\Service;

use Zend\Db\TableGateway\TableGateway;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class MultiServers
{
    protected $_sm;
    protected $_options = array();
    protected $_adapters = array();
    
    public function __construct(ServiceLocatorInterface $serviceLocator, $options = array()) 
    {
        $this->_sm = $serviceLocator->get('config');
        $this->_options = $options;
        return $this;
    }
    
    public function getSm()
    {
        return $this->_sm;
    }
    
    public function getExecMasterPos()
    {
        if($this->_exec_master_log_pos === null){
            $this->getSm()->get('Zend\Db\Adapter\Adapter')->query('SHOW SLAVE STATUS');
        }
    }
    public function getRedis($className)
    {
        if(SERVER_ID == 0){
            if($this->isRemoveTable($className)){
                $serverRow = $this->getSm()->get('TakeServer\Service')->getServerRow();
                return $this->_redis[$serverRow->id] = \Zend\Cache\StorageFactory::factory(array(
                    'adapter' => array(
                        'name'      =>  '\Ap\Cache\Storage\Adapter\Redis',
                        'options'   =>  array(
                            'host'      =>  $serverRow->db_host,
                            'port'      =>  '6379',
                            'prefix'    =>  'apocalypse_'
                        )
                    ),
                    'plugins' => array(
                        'IgnoreUserAbort' => array(
                            'exitOnAbort' => true
                        ),
                    )
                ));
            }
            return false;
        }
        return $this->getSm()->get('Cache\Redis');
    }
    
    public function createTableGateway($tableName, $resultSetPrototype)
    {
        if(!$this->isReplicationTables($tableName)){
            return new TableGateway($tableName,  $this->getAdapter('slave'), null, $resultSetPrototype);
        }
        return new MultiServers\TableGateway($tableName, $this->getAdapter('master'), $this->getAdapter('slave'), $resultSetPrototype);
    }
    
    public function getAdapter($type = 'master')
    {
        if(!isset($this->_adapters[$type])){
            $this->_adapters[$type] = new \Zend\Db\Adapter\Adapter($this->_options['db'.$type]);
        }
        return $this->_adapters[$type];
    }
    
    public function isReplicationTables($tableName)
    {
        $tables = array_keys($this->_options['replicationTables']);
        return ((count($tables) == 1 && $tables[0]=='*') 
                || in_array($tableName, $tables));
    }
    
    public function getAdapterByTable($tableName, $read = false)
    {
        if($read and $this->isReplicationTables($tableName)){
            return $this->getAdapter('master');
        }
        return $this->getAdapter('slave');
    }
    
    public function createService()
    {
        return $this->getAdapter('slave');
    }
    
}
