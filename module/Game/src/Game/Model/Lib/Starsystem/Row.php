<?php

namespace Game\Model\Lib\Starsystem;

use Ap\Model\Row as Prototype;

class Row extends Prototype
{
    protected $_inputFilter, $_planet_rowset, $_gate_rowset;
    
    public function getPlanetRowset()
    {
        if($this->_planet_rowset === null){
            $this->_planet_rowset = $this->getSm()->get('Lib\Starsystem\Planet\Table')->fetchAllBy('starsystem_id', $this->id);
        }
        return $this->_planet_rowset;
    }
    
    public function getGateRowset()
    {
        if($this->_gate_rowset === null){
            $this->_gate_rowset = $this->getSm()->get('Lib\Starsystem\Gate\Table')->fetchAllBy('starsystem_id', $this->id);
        }
        return $this->_gate_rowset;
    }
    
    public function getInputFilter()
    {
        if($this->_inputFilter === null){
            $inputFilter = new \Ap\InputFilter\InputFilter;
            $inputFilter->add(array(
                'name' => 'title',
                'required' => true,
            ));
            $inputFilter->add(array(
                'name' => 'filename',
                'required' => false,
                'filters'   =>  array(
                    array(
                        'name'=>'StringTrim'
                    ),
                    array(
                        'name'=>'ToNull'
                    )
                )
            ));
            $this->_inputFilter = $inputFilter;
        }
        return $this->_inputFilter;
    }
}