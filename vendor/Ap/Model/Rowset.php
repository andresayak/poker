<?php

namespace Ap\Model;

use Zend\Db\ResultSet\ResultSet;
use Zend\Cache\Storage\StorageInterface;
use Zend\Db\ResultSet\Exception;
use Zend\Db\Adapter\Driver\ResultInterface;

class Rowset extends ResultSet
{
    protected $_items;
    protected $_sm, $_table;
    protected $_tableName = false;
    protected $_rowName = false;
    protected $_index = array();
    
    public function setTable($table)
    {
        $this->_table = $table;
        if($this->count){
            foreach($this->getItems() AS $item){
                $item->setTable($table);
            }
        }
        return $this;
    }
    
    public function getTable()
    {
        return $this->_table;
    }
    
    public function setOption($name, $value)
    {
        $this->_options[$name] = $value;
        return $this;
    }
    
    
    public function getOption($name)
    {
        return $this->_options[$name];
    }
    
    public function getSm()
    {
        if($this->_sm === null){
            throw new \Exception('Service manager not set');
        }
        return $this->_sm;
    }
    
    public function setSm($sm)
    {
        $this->_sm = $sm;
        if($this->count){
            foreach($this->getItems() AS $item){
                $item->setSm($sm);
            }
        }
        return $this;
    }
    
    public function getCount()
    {
        return count($this->getItems());
    }
    
    public function __sleep()
    {
        $this->getItems();
        return array('_items', '_index');
    }
    
    public function __wakeup()
    {
        $this->count = count($this->_items);
    }
    
    public function add(Row $row)
    {
        $this->count++;
        $this->getItems();
        $this->_items[] = $row;
        return $this;
    }
    
    
    public function getItems()
    {
        if(null === $this->_items) {
            $this->_items = new \ArrayObject;
            if($this->getTable() and $this->getTable()->getKey()){
                $key = $this->getTable()->getKey();
            }else{
                $key = false;
            }
            foreach ($this AS $row){
                $position = count($this->_items);
                $this->_items[$position] = $row;
                if($key){
                    $this->addToIndex($key, $row->{$key}, $position);
                }
            }
        }
        return $this->_items;
    }
    
    public function setItems($items)
    {
        $this->_items = $items;
        $this->count = count($items);
        return $this;
    }

    public function getItem($index)
    {
        $items = $this->getItems();
        return $items[$index];
    }
    
    public function getById($id)
    {
        return $this->getBy('id', $id);
    }
    
    public function addToIndex($key, $value, $position)
    {
        $this->_index[$key][$value] = $position;
    }
    
    public function getBy($key, $value)
    {
        $items = $this->getItems();
        if(isset($this->_index[$key]) and isset($this->_index[$key][$value])){
            $index = $this->_index[$key][$value];
            return $items[$index];
        }
        $iterator = $items->getIterator();
        $iterator->rewind();
        while($iterator->valid()) {
            if($iterator->current()->{$key} == $value){
                $this->addToIndex($key, $value, $iterator->key());
                return $iterator->current();
            }
            $iterator->next();
        }
        /*
        for($i=0;$i<count($items);$i++){
            if($items->offsetGet($i)->{$key} == $value){
                return $items->offsetGet($i);
            }
        }*/
        return false;
    }
    
    public function getRowsetBy($func, $value = null, $orderby = null, $sort = 'ASC')
    {
        if(!$func instanceof \Closure){
            $key = $func;
            $func = function($row) use($key, $value){
                return (($value!==null and $row->{$key} == $value) 
                    or ($value === null and $row->{$key} === null));
            };
        }
        $class = get_class($this);
        $rowset = new $class;
        $rowset->setTable($this->getTable());
        $rowset->setArrayObjectPrototype($this->getArrayObjectPrototype());
        $data = new \ArrayObject;
        foreach($this->getItems() AS $row){
            if($func($row)){
                $data[] = $row;
            }
        }
        
        if($orderby){
            $sortCallback = function($a, $b) use($orderby, $sort){
                if($a->{$orderby} == $b->{$orderby}){
                    return 0;
                }
                return ($a->{$orderby} > $b->{$orderby} and $sort == 'DESC') ?+1:-1;
            };
            $data->uasort($sortCallback);
        }
        $rowset->initialize($data);
        return $rowset;
    }
    
    public function maxValue($key)
    {
        $max = null;
        foreach($this->getItems() AS $row){
            $max = max($max, $row->$key);
        }
        return $max;
    }
    public function minValue($key)
    {
        $min = null;
        foreach($this->getItems() AS $row){
            $min = ($min===null)?$row->$key:min($min, $row->$key);
        }
        return $min;
    }
    
    public function sumValue($key)
    {
        $sum = 0;
        foreach($this->getItems() AS $row){
            $sum+= $row->$key;
        }
        return $sum;
    }
    
    public function getRand()
    {
        $items = $this->getItems();
        return $items[rand(0, count($items)-1)];
    }
    
    public function getRandRowset($count)
    {
        $indexes = array_rand(range(0, count($this->getItems())-1), $count);
        if(!is_array($indexes)){
            $indexes = array($indexes);
        }
        $class = get_class($this);
        $rowset = new $class;
        $rowset->setArrayObjectPrototype($this->getArrayObjectPrototype());
        $data = new \ArrayObject;
        
        foreach($this->getItems() AS $index=>$row){
            if(in_array($index, $indexes)){
                $data[] = $row;
            }
        }
        $rowset->initialize($data);
        return $rowset;
    }
    
    public function remove($index)
    {
        $this->getItems();
        unset($this->_items[$index]);
        return $this;
    }
    
    public function removeByIds($ids)
    {
        if(!count($ids)){
            return $this;
        }
        $indexes = array();
        foreach($this->getItems() AS $index=>$item){
            if(in_array($item->id, $ids)){
                $indexes[] = $index;
            }
        }
        foreach($indexes AS $index){
            $this->remove($index);
        }
        return $this;
    }
    
    public function delete()
    {
        foreach($this->getItems() AS $row){
            if($row->getTable()){
                $row->delete();
            }
        }
        $this->_items = array();
        $this->count = 0;
        return $this;
    }
    
    public function toArrayForApi($recursive = true)
    {
        $data = array();
        foreach($this->getItems() AS $row){
            if($recursive and method_exists($row, 'toArrayForApi')){
                $data[] = $row->toArrayForApi();
            }else{
                $data[] = $row->toArray();
            }
        }
        return $data;
    }
    
    public function toArray()
    {
        $return = array();
        foreach ($this->getItems() as $row) {
            if (is_array($row)) {
                $return[] = $row;
            } elseif (method_exists($row, 'toArray')) {
                $return[] = $row->toArray();
            } elseif (method_exists($row, 'getArrayCopy')) {
                $return[] = $row->getArrayCopy();
            } else {
                throw new Exception\RuntimeException(
                    'Rows as part of this DataSource, with type ' . gettype($row) . ' cannot be cast to an array'
                );
            }
        }
        return $return;
    }
    
    public function getValues($key)
    {
        $data = array();
        foreach($this->getItems() AS $row){
            $data[] = $row->{$key};
        }
        return $data;
    }
}