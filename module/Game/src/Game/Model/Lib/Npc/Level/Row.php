<?php

namespace Game\Model\Lib\Npc\Level;

use Ap\Model\Row as Prototype;

class Row extends Prototype
{
    use \Game\Model\Lib\Npc\Provider;
    
    public function getInputFilter()
    {
        if($this->_inputFilter === null){
            $inputFilter = new \Ap\InputFilter\InputFilter;
            $inputFilter->add(array(
                'name' => 'level',
                'required' => true,
            ));
            $inputFilter->add(array(
                'name' => 'def',
                'required' => true,
            ));
            $inputFilter->add(array(
                'name' => 'prison_rate',
                'required' => true,
            ));
            $this->_inputFilter = $inputFilter;
        }
        return $this->_inputFilter;
    }
    
    public function getLootRowset()
    {
        if($this->_loot_rowset === null){
            $this->_loot_rowset = $this->getSm()->get('Lib\Npc\Level\Loot\Table')
                ->fetchAllBy('level_id', $this->id);
        }
        return $this->_loot_rowset;
    }
}