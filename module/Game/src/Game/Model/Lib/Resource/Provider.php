<?php

namespace Game\Model\Lib\Resource;

trait Provider {

    use \Game\Provider\ProvidesServiceManager;
    
    protected $_resource_row;

    public function getResourceRow() 
    {
        if($this->_resource_row === null){
            $this->_resource_row = $this->getSm()->get('Lib\Resource\Table')->getRowset()->getBy('code', $this->resource_code);
        }
        return $this->_resource_row;
    }

    public function setResourceRow(Row $resourceeRow) 
    {
        $this->_resource_row = $resourceeRow;
        $this->resource_code = $resourceeRow->code;
        return $this;
    }
}
