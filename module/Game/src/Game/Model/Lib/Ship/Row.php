<?php

namespace Game\Model\Lib\Ship;

use Ap\Model\Row as Prototype;

class Row extends Prototype
{
    protected $_inputFilter;
    protected $_weapon_rowset, $_need_rowset, $_attribute_rowset, $_slot_rowset;
    
    public function getInputFilter()
    {
        if($this->_inputFilter === null){
            $inputFilter = new \Ap\InputFilter\InputFilter;
            $inputFilter->add(array(
                'name' => 'code',
                'required' => true,
            ));
            $inputFilter->add(array(
                'name' => 'building_code',
                'required' => false,
            ));
            $inputFilter->add(array(
                'name' => 'title',
                'required' => true,
            ));
            $inputFilter->add(array(
                'name' => 'filename',
                'required' => true,
            ));
            $inputFilter->add(array(
                'name' => 'type',
                'required' => true,
            ));
            $this->_inputFilter = $inputFilter;
        }
        return $this->_inputFilter;
    }
    
    public function getWeaponRowset()
    {
        if($this->_weapon_rowset === null){
            $this->_weapon_rowset = $this->getSm()->get('Lib\Ship\Weapon\Table')
                ->fetchAllBy('ship_code', $this->code);
        }
        return $this->_weapon_rowset;
    }
    
    public function getSlotRowset()
    {
        if($this->_slot_rowset === null){
            $this->_slot_rowset = $this->getSm()->get('Lib\Ship\Slot\Table')
                ->fetchAllBy('ship_code', $this->code);
        }
        return $this->_slot_rowset;
    }
    
    public function getNeedRowset()
    {
        if($this->_need_rowset === null){
            $this->_need_rowset = $this->getSm()->get('Lib\Ship\Need\Table')
                ->fetchAllBy('ship_code', $this->code);
        }
        return $this->_need_rowset;
    }
    
    public function getAttributeRowset()
    {
        if($this->_attribute_rowset === null){
            $this->_attribute_rowset = $this->getSm()->get('Lib\Ship\Attribute\Table')
                ->fetchAllBy('ship_code', $this->code);
        }
        return $this->_attribute_rowset;
    }
}