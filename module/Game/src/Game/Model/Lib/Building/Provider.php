<?php

namespace Game\Model\Lib\Building;

trait Provider {

    use \Game\Provider\ProvidesServiceManager;
    
    protected $_building_row;

    public function getBuildingRow() 
    {
        if($this->_building_row === null){
            $this->_building_row = $this->getSm()->get('Lib\Building\Table')->getRowset()->getBy('code', $this->building_code);
        }
        return $this->_building_row;
    }

    public function setBuildingRow(Row $buildingRow) 
    {
        $this->_building_row = $buildingRow;
        $this->building_code = $buildingRow->code;
        return $this;
    }
}
