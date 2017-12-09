<?php

namespace Game\Model\Lib\Ship\Slot;

use Ap\Model\Row as Prototype;

class Row extends Prototype
{
    use \Game\Model\Lib\Ship\Provider;
    
    public function getInputFilter()
    {
        if($this->_inputFilter === null){
            $inputFilter = new \Ap\InputFilter\InputFilter;
            $inputFilter->add(array(
                'name' => 'name',
                'required' => true,
            ));
            $inputFilter->add(array(
                'name' => 'x',
                'required' => true,
            ));
            $inputFilter->add(array(
                'name' => 'y',
                'required' => true,
            ));
            $this->_inputFilter = $inputFilter;
        }
        return $this->_inputFilter;
    }
}