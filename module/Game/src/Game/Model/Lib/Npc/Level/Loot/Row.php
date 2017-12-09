<?php

namespace Game\Model\Lib\Npc\Level\Loot;

use Ap\Model\Row as Prototype;

use Game\Model\Lib\Npc\Level\Provider AS LevelProvider;
use Game\Model\Lib\Resource\Provider AS ResourceProvider;

class Row extends Prototype
{
    use LevelProvider,
        ResourceProvider{
        ResourceProvider::getSm insteadof LevelProvider;
        ResourceProvider::setSm insteadof LevelProvider;
    }
    
    public function getInputFilter()
    {
        if($this->_inputFilter === null){
            $inputFilter = new \Ap\InputFilter\InputFilter;
            $inputFilter->add(array(
                'name' => 'resource_code',
                'required' => true,
            ));
            $inputFilter->add(array(
                'name' => 'count',
                'required' => true,
                'validators'    =>  array(
                    array(
                        'name'  =>  'Int'
                    )
                )
            ));
            $inputFilter->add(array(
                'name' => 'probability',
                'required' => true,
                'validators'    =>  array(
                    array(
                        'name'  =>  'Int'
                    )
                )
            ));
            $inputFilter->add(array(
                'name' => 'diff',
                'required' => true,
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
}