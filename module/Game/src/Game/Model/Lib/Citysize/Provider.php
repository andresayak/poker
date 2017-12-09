<?php

namespace Game\Model\Lib\Citysize;

trait Provider {

    use \Game\Provider\ProvidesServiceManager;
    
    protected $_citysize_row;

    public function getCitysizeRow() 
    {
        if($this->_citysize_row === null){
            $this->_citysize_row = $this->getSm()->get('Lib\Citysize\Table')->fetchBy('id', $this->citysize_id);
        }
        return $this->_citysize_row;
    }

    public function setCitysizeRow(Row $citysizeRow) 
    {
        $this->_citysize_row = $citysizeRow;
        return $this;
    }
}
