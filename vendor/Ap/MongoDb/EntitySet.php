<?php

namespace Ap\MongoDb;

use Iterator;
use ArrayIterator;

class EntitySet implements Iterator {

    protected $dataSource = array();
    protected $count = null;
    protected $fieldCount = null;
    protected $position = 0;
    protected $_sm;
    protected $_entityPrototype;
    protected $_collection;
    
    public function getEntityPrototype()
    {
        return $this->_entityPrototype;
    }
    
    public function setEntityPrototype(Entity $entity)
    {
        $this->_entityPrototype = $entity;
        return $this;
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
    
    public function exchangeArray($dataSource) {
        
        if (is_array($dataSource)) {
            $first = current($dataSource);
            reset($dataSource);
            $this->count = count($dataSource);
            $this->fieldCount = count($first);
            $this->dataSource = new ArrayIterator($dataSource);
        } elseif ($dataSource instanceof IteratorAggregate) {
            $this->dataSource = $dataSource->getIterator();
        } elseif ($dataSource instanceof Iterator) {
            $this->dataSource = $dataSource;
        } else {
            throw new Exception\InvalidArgumentException('DataSource provided is not an array, nor does it implement Iterator or IteratorAggregate');
        }
        return $this;
    }

    public function toArray() {
        $list = array();
        $this->rewind();
        while($this->valid()){
            $list[] = $this->current()->toArray();
            $this->next();
        }
        return $list;
    }

    public function next() {
        $this->dataSource->next();
        $this->position++;
    }

    /**
     * Iterator: retrieve current key
     *
     * @return mixed
     */
    public function key() {
        return $this->position;
    }

    /**
     * Iterator: get current item
     *
     * @return array
     */
    public function current()
    {
        $data = $this->dataSource->current();
        
        if(is_array($data)){
            $entity = clone $this->getEntityPrototype();
            $entity->exchangeArray($data);
            return $entity;
        }
        return $data;
    }

    /**
     * Iterator: is pointer valid?
     *
     * @return bool
     */
    public function valid() {
        if ($this->dataSource instanceof Iterator) {
            return $this->dataSource->valid();
        } else {
            $key = key($this->dataSource);
            return ($key !== null);
        }
    }

    /**
     * Iterator: rewind
     *
     * @return void
     */
    public function rewind() {
        if ($this->dataSource instanceof Iterator) {
            return $this->dataSource->rewind();
        } else {
            $this->position = 0;
        }
        return $this;
    }

    /**
     * Countable: return count of rows
     *
     * @return int
     */
    public function count() {
        if ($this->count !== null) {
            return $this->count;
        }
        $this->count = count($this->dataSource);
        return $this->count;
    }
}
