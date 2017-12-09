<?php

namespace Game\Model\Lib\Unit;

use Ap\Model\Row as Prototype;

class Row extends Prototype
{
    use \Game\Model\Lib\Building\Provider;
    
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
                'required' => true,
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
    
    public function getNeedRowset()
    {
        if($this->_need_rowset === null){
            $this->_need_rowset = $this->getSm()->get('Lib\Unit\Need\Table')
                ->fetchAllBy('unit_code', $this->code);
        }
        return $this->_need_rowset;
    }
    
    public function getAttributeRowset()
    {
        if($this->_attribute_rowset === null){
            $this->_attribute_rowset = $this->getSm()->get('Lib\Unit\Attribute\Table')
                ->fetchAllBy('unit_code', $this->code);
        }
        return $this->_attribute_rowset;
    }
    
    public function getDependRowset()
    {
        if($this->_depend_rowset === null){
            $this->_depend_rowset = $this->getSm()->get('Lib\Unit\Depend\Table')
                ->fetchAllBy('unit_code', $this->code);
        }
        return $this->_depend_rowset;
    }
}