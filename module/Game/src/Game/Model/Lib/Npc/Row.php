<?php

namespace Game\Model\Lib\Npc;

use Ap\Model\Row as Prototype;

class Row extends Prototype
{
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
            $this->_inputFilter = $inputFilter;
        }
        return $this->_inputFilter;
    }
    
    public function getGuardRowset()
    {
        if($this->_guard_rowset === null){
            $this->_guard_rowset = $this->getSm()->get('Lib\Npc\Guard\Table')
                ->fetchAllBy('npc_code', $this->code);
        }
        return $this->_guard_rowset;
    }
    
    public function getLevelRowset()
    {
        if($this->_level_rowset === null){
            $this->_level_rowset = $this->getSm()->get('Lib\Npc\Level\Table')
                ->fetchAllBy('npc_code', $this->code);
        }
        return $this->_level_rowset;
    }
}