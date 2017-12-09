<?php

namespace Ap\Model;

use Zend\Db\TableGateway\TableGateway;
use Ap\Model\Rowset;
use Zend\Db\Sql\Where;
use Zend\Db\Sql\Expression;
use Zend\Db\Sql\Predicate\Expression AS PredicateExpression;
use Zend\Db\Sql\Select;
use Zend\Db\TableGateway\Exception\InvalidArgumentException;

class Table 
{
    protected $_name;
    protected $_sm, $_rowset;
    protected $_key = 'id';
    protected $_cols = array();
    protected $_sleeps = array();
    protected $_defaults = array();
    protected $_rowClass = 'Row';
    protected $_counters = array();
    protected $_rowsetClass = 'Rowset';
    protected $_tableGateway;   
    protected $_cache_status = true;
    protected $_uniqs = array();
    protected $_blocks_ids = array();
    protected $_cache_cols = array();
    protected $_filters = array();
    protected $_cache_adapter;
    
    public function filterData(& $data)
    {
        foreach($data AS $col=>$value){
            $this->filterCol($col, $value);
            $data[$col] = $value;
        }
    }
    
    public function filterCol($col, &$value) 
    {
        if (isset($this->_filters[$col])) {
            if (isset($this->_filters[$col]['type'])) {
                settype($value, $this->_filters[$col]['type']);
                
            }
        }
    }

    public function getCounters()
    {
        return $this->_counters;
    }
    
    public function getCacheAdapter()
    {
        if($this->_cache_adapter === null){
            $this->_cache_adapter = $this->getSm()->get('Cache\Redis');
        }
        return $this->_cache_adapter;
    }
    
    public function setCacheAdapter($cacheAdapter)
    {
        $this->_cache_adapter = $cacheAdapter;
        return $this;
    }
    
    public function addBlockId($id)
    {
        $this->_blocks_ids[$id] = true;
        return $this;
    }
    
    public function isBlockId($id)
    {
        return (isset($this->_blocks_ids[$id]));
    }
    
    public function removeFromBlockId($id)
    {
        if(isset($this->_blocks_ids[$id])){
            unset($this->_blocks_ids[$id]);
        }
        return $this;
    }
    
    public function getCols()
    {
        return $this->_cols;
    }
    
    public function getCacheCols()
    {
        return $this->_cache_cols;
    }
    
    public function getSleeps()
    {
        return $this->_sleeps;
    }
    
    public function getDefaults()
    {
        return $this->_defaults;
    }
    
    public function __construct($sm, $dbName = false)
    {
        $this->setSm($sm);
        if($sm->has('MultiServers\Service')){
            $this->_tableGateway = $sm->get('MultiServers\Service')->createTableGateway($this->_name, $this->resultSetPrototype());
        }else{
            $dbName = ($dbName!==false)?$dbName:'Zend\Db\Adapter\Adapter';
            $db = $this->getSm()->get($dbName);
            $this->_tableGateway = new TableGateway($this->_name, $db, null, $this->resultSetPrototype());
        }
    }
    
    public function getRowset()
    {
        if($this->_rowset === null){
            $this->_rowset = $this->fetchAll();
        }
        return $this->_rowset;
    }
    
    public function reloadDb()
    {
        if($this->getSm()->has('MultiServers\Service')){
            $db = $this->getSm()->get('MultiServers\Service')->getDbAdapter(get_class($this), $this->_name);
            try {
                $redis = $this->getSm()->get('MultiServers\Service')->getRedis(get_class($this), $this->_name);
            } catch (\Zend\Cache\Exception\ExtensionNotLoadedException $e) {
                if(SERVER_ID != 0){
                    throw new \Exception('not found redis', null, $e);
                }
                $redis = false;
            }
            if($redis){
                $this->setCacheAdapter($redis);
            }
        }else{
            $dbName = ($dbName!==false)?$dbName:'Zend\Db\Adapter\Adapter';
            $db = $this->getSm()->get($dbName);
        }
        $this->_tableGateway = new TableGateway($this->_name, $db, null, $this->resultSetPrototype());
        return $this;
    }
    
    public function resultSetPrototype()
    {
        $path = explode('\\', get_class($this));
        array_pop($path);
        $baseClass = implode('\\',$path);
        $rowsetClass =  $baseClass.'\\'.$this->_rowsetClass;
        if(!class_exists($rowsetClass))
            $rowsetClass = __NAMESPACE__.'\\'.$this->_rowsetClass;
        $resultSetPrototype = new $rowsetClass;
        $rowClass =  $baseClass.'\\'.$this->_rowClass;
        if(!class_exists($rowClass))
            $rowClass = __NAMESPACE__.'\\'.$this->_rowClass;
        $rowPrototype = new $rowClass;
        $rowPrototype->setTable($this);
        $rowPrototype->setSm($this->getSm());
        return $resultSetPrototype->setSm($this->getSm())->setTable($this)->setArrayObjectPrototype($rowPrototype);
    }
    
    public function getTableGateway()
    {
        return $this->_tableGateway;
    }
    
    public function setTableGateway(TableGateway $tableGateway)
    {
        $this->_tableGateway = $tableGateway;
        return $this;
    }

    public function cached($function, $suffix)
    {
        $cacheStatus = false;
        if($this->_cache_status){
            $service = $this->getSm()->get('CacheManager');
            if($service->getStatus() and $cache = $service->isCacheByTable($this)){
                $cacheStatus = true;
                $key = str_replace('\\', '_', get_class($this).md5($suffix));
                $result = $cache->getItem($key);
                if($result !== null){
                    if($result instanceof Rowset){
                        $result->setSm($this->getSm());
                        $result->setArrayObjectPrototype($this->resultSetPrototype()->getArrayObjectPrototype());
                        $result->setTable($this);
                    }elseif($result instanceof Row){
                        $result->setSm($this->getSm());
                        $result->setTable($this);
                    }
                    return $result;
                }
            }
        }
        $this->getTableGateway()->initialize();
        $result = $function();
        
        if($cacheStatus){
            $cache->setItem($key, $result);
        }
        return $result;
    }
    
    public function fetchAll()
    {
        $tableGataway = $this->getTableGateway();
        return $this->cached(function() use($tableGataway){
            return $tableGataway->select();
        }, '->fetchAll()');
    }
    
    public function fetchBy($col, $value)
    {
        $this->getTableGateway()->initialize();
        $tableGataway = $this->getTableGateway();
        return $this->cached(function() use($col, $value, $tableGataway){
            if(is_array($value)){
                $select = $this->getTableGateway()->getSql()->select();
                $select->where(function (Where $where) use ($col, $value){
                    $where->in($col, $value);
                });
                return  $this->getTableGateway()->selectWith($select);
            }else
                return $this->getTableGateway()->select(array($col => $value))->current();
        }, '->fetchBy('.print_r($col, true).', '.print_r($value, true).')');
    }
    
    public function fetchAllBy($col, $value)
    {
        $this->getTableGateway()->initialize();
        $tableGataway = $this->getTableGateway();
        return $this->cached(function() use($col, $value, $tableGataway){
            return $this->getTableGateway()->select(array($col => $value));
        }, '->fetchAllBy('.$col.', '.$value.')');
        
    }
    
    public function fetchByPK($value)
    {
        return $this->fetchBy($this->getKey(), $value);
    }
    
    public function fetchByPKForUpdate($id)
    {
        if($this->getTableGateway() instanceof \Ap\Service\MultiServers\TableGateway){
            $adapter = $this->getTableGateway()->get('master')->getAdapter();
        }else{
            $adapter = $this->getTableGateway()->getAdapter();
        }
        
        $qi = function($name) use ($adapter) { return $adapter->platform->quoteIdentifier($name); };
        $fp = function($name) use ($adapter) { return $adapter->driver->formatParameterName($name); };
        
        
        $sql = "SELECT * FROM ".$qi($this->getName())
            ." WHERE ".$qi($this->getKey())." = ".$fp('id')
            ." FOR UPDATE";
        $statement = $adapter->query($sql);
        $result = $statement->execute(array('id' => $id));
        
        $resultSet = clone $this->resultSetPrototype();
        $resultSet->initialize($result);
        
        return $resultSet->current();
    }
    
    public function fetchByArray(array $arg, $row = false)
    {
        $rowSet = $this->getTableGateway()->select($arg);
        return (!$row) ? $rowSet:$rowSet->current();
    }
    
    public function insert(Row $row)
    {
        $data = $row->toArrayForSave();
        $this->getTableGateway()->insert($data);
        if($value = ($this->getTableGateway()->getLastInsertValue())){
            $row->{$this->getKey()} = $value;
        }
        foreach($this->getCacheCols() AS $col){
            $row->setCacheCol($col, $row->$col);
        }
    }
    
    public function saveRow(Row $row, $force_insert = false)
    {
        if ($row->{$this->getKey()} === null or $force_insert) {
            $this->insert($row);
        } else {
            $data = $row->toArrayForSave();
            if(count($data))
                $this->getTableGateway()->update($data, array($this->getKey() => $row->{$this->getKey()}));
        }
        $this->removeFromBlockId($row->{$this->getKey()});
    }
    
    public function syncRow(Row $row)
    {
        $data = $row->toArrayForSync();
        if(count($data)){
            $this->getTableGateway()->update($data, array($this->getKey() => $row->{$this->getKey()}));
        }
    }

    public function deleteRow(Row $row)
    {
        $this->getTableGateway()->delete(array($this->getKey() => $row->{$this->getKey()}));
    }
    
    public function createRow($data = array(), $save = false)
    {
        $resultSet = $this->getTableGateway()->getResultSetPrototype();
        $newRow = clone $resultSet->getArrayObjectPrototype();
        
        $newRow->setForceInsert(true)->exchangeArray($data + $this->getDefaults());
        if($save)
            $this->saveRow($newRow);
        return $newRow;
    }

    public function getName()
    {
        return $this->_name;
    }
    
    public function getSm()
    {
        return $this->_sm;
    }
    
    public function setSm($sm)
    {
        $this->_sm = $sm;
        return $this;
    }
    
    public function getKey()
    {
        return $this->_key;
    }
    
    public function useAdapter($dbName)
    {
        $db = $this->getSm()->get($dbName);
        $this->_tableGateway = new TableGateway($this->_name, $db, null, $this->resultSetPrototype());
    }
}
