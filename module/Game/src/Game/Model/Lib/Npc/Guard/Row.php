<?php

namespace Game\Model\Lib\Npc\Guard;

use Ap\Model\Row as Prototype;
use Game\Model\Lib\Unit\Provider AS UnitProvider;
use Game\Model\Lib\Npc\Provider AS NpcProvider;

class Row extends Prototype
{
    
    use UnitProvider,
        NpcProvider{
        NpcProvider::getSm insteadof UnitProvider;
        NpcProvider::setSm insteadof UnitProvider;
    }
    
    public function getInputFilter()
    {
        if($this->_inputFilter === null){
            $inputFilter = new \Ap\InputFilter\InputFilter;
            $inputFilter->add(array(
                'name' => 'unit_code',
                'required' => true,
            ));
            $inputFilter->add(array(
                'name' => 'probability',
                'required' => false,
                'validators'    =>  array(
                    array(
                        'name'  =>  'Int'
                    )
                )
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