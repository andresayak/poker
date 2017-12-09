<?php

namespace Game\Model\Lib\Resource;

trait ProviderWeapon {

    use \Game\Provider\ProvidesServiceManager;
    
    protected $_resource_row;

    public function getWeaponRow() 
    {
        if($this->_resource_row === null){
            $this->_resource_row = $this->getSm()->get('Lib\Resource\Table')->fetchBy('code', $this->resource_code);
        }
        return $this->_resource_row;
    }

    public function setWeaponRow(Row $weaponRow) 
    {
        if($weaponRow->type != 'weapon'){
            throw new Exception\InvalidTypeException('Type is not weapon');
        }
        $this->_resource_row = $weaponRow;
        return $this;
    }
}
