<?php

namespace Game\Model\Lib\Building;

use Ap\Model\Row as Prototype;

class Row extends Prototype
{
    protected $_level_rowset, $_inputFilter;
    
    public function getInputFilter()
    {
        if($this->_inputFilter === null){
            $inputFilter = new \Ap\InputFilter\InputFilter;
            $inputFilter->add(array(
                'name' => 'code',
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
            $inputFilter->add(array(
                'name' => 'limit',
                'required' => true,
            ));
            $this->_inputFilter = $inputFilter;
        }
        return $this->_inputFilter;
    }
    
    public function getLevelRowset()
    {
        if($this->_level_rowset === null){
            $this->_level_rowset = $this->getSm()->get('Lib\Building\Level\Table')
                ->fetchAllBy('building_code', $this->code);
        }
        return $this->_level_rowset;
    }
}