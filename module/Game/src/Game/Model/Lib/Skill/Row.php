<?php

namespace Game\Model\Lib\Skill;

use Ap\Model\Row as Prototype;

class Row extends Prototype
{
    use \Game\Model\Lib\Building\Provider;
    
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
            $this->_level_rowset = $this->getSm()->get('Lib\Skill\Level\Table')
                ->fetchAllBy('skill_code', $this->code);
        }
        return $this->_level_rowset;
    }
}