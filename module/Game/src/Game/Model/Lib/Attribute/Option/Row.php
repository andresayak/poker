<?php

namespace Game\Model\Lib\Attribute\Option;

use Ap\Model\Row as Prototype;

class Row extends Prototype
{
    use \Game\Model\Lib\Attribute\Provider;
    
    public function getInputFilter()
    {
        if($this->_inputFilter === null){
            $inputFilter = new \Ap\InputFilter\InputFilter;
            $inputFilter->add(array(
                'name' => 'title',
                'required' => true,
            ));
            $inputFilter->add(array(
                'name' => 'value',
                'required' => true,
            ));
            $this->_inputFilter = $inputFilter;
        }
        return $this->_inputFilter;
    }
}