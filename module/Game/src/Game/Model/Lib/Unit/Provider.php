<?php

namespace Game\Model\Lib\Unit;

trait Provider {

    use \Game\Provider\ProvidesServiceManager;
    
    protected $_unit_row;

    public function getUnitRow() 
    {
        if($this->_unit_row === null){
            $this->_unit_row = $this->getSm()->get('Lib\Unit\Table')->fetchBy('code', $this->unit_code);
        }
        return $this->_unit_row;
    }

    public function setUnitRow(Row $unitRow) 
    {
        $this->_unit_row = $unitRow;
        $this->unit_code = $unitRow->code;
        return $this;
    }
}
