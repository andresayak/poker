<?php

namespace Ap\Service\MultiServers;

use Zend\Db\Adapter\AdapterInterface;
use Zend\Db\TableGateway\TableGatewayInterface;
use Zend\Db\ResultSet\ResultSet;
use Zend\Db\ResultSet\ResultSetInterface;
use Zend\Db\Sql\Delete;
use Zend\Db\Sql\Insert;
use Zend\Db\Sql\Select;
use Zend\Db\Sql\Sql;
use Zend\Db\Sql\TableIdentifier;
use Zend\Db\Sql\Update;
use Zend\Db\Sql\Where;
use Zend\Db\TableGateway\Feature\EventFeature;

class TableGateway implements TableGatewayInterface {

    public function __construct($table, AdapterInterface $adapterMaster, AdapterInterface $adapterSlave = null, ResultSetInterface $resultSetPrototype = null)
    {
        $this->table = $table;
        $this->resultSetPrototype = $resultSetPrototype;
        $this->masterGateway = new \Zend\Db\TableGateway\TableGateway($table, $adapterMaster, null, $resultSetPrototype);
        $this->slaveGateway = new \Zend\Db\TableGateway\TableGateway($table, $adapterSlave, null, $resultSetPrototype);
        $this->initialize('master');
        $this->initialize('slave');
        return $this;
    }
    
    public function get($type = 'master')
    {
        if($type == 'master'){
            return $this->masterGateway;
        }
        return $this->slaveGateway;
    }
    
    public function initialize($type = 'master')
    {
        if($type == 'master'){
            $this->masterGateway->initialize();
            return $this->masterGateway;
        }
        $this->slaveGateway->initialize();
        return $this->slaveGateway;
    }
    
    public function getSql($type = 'master')
    {
        if($type == 'master'){
            $this->masterGateway->initialize();
            return $this->masterGateway->getSql();
        }
        $this->slaveGateway->initialize();
        return $this->slaveGateway->getSql();
    }
    
    public function getResultSetPrototype()
    {
        return $this->resultSetPrototype;
    }
    
    public function select($where = null)
    {
        return $this->slaveGateway->select($where);
    }
    
    public function selectWith(Select $select)
    {
        return $this->slaveGateway->selectWith($select);
    }
    
    public function getTable()
    {
        return $this->table;
    }
    
    public function insert($set)
    {
        return $this->masterGateway->insert($set);
    }
    
    public function insertWith(Insert $insert)
    {
        return $this->masterGateway->insertWith($insert);
    }
    
    public function update($set, $where = null)
    {
        return $this->masterGateway->update($set, $where);
    }
    
    public function updateWith(Update $update)
    {
        return $this->masterGateway->updateWith($update);
    }
    
    public function delete($where)
    {
        return $this->masterGateway->delete($where);
    }
    
    public function deleteWith(Delete $delete)
    {
        return $this->masterGateway->deleteWith($delete);
    }
    
    public function getLastInsertValue()
    {
        return $this->masterGateway->getLastInsertValue();
    }
}