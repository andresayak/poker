<?php

namespace Game\Model\Lib\Starsystem\Planet;

use Ap\Model\Row as Prototype;

class Row extends Prototype
{
    use \Game\Model\Lib\Starsystem\Provider;
    
    protected $_inputFilter;
    
    public function getInputFilter()
    {
        if($this->_inputFilter === null){
            $inputFilter = new \Ap\InputFilter\InputFilter;
            $inputFilter->add(array(
                'name' => 'title',
                'required' => true,
            ));
            $inputFilter->add(array(
                'name' => 'size',
                'required' => true,
                'validators'    =>  array(
                    array(
                        'name'  =>  'Int'
                    )
                )
            ));
            $inputFilter->add(array(
                'name' => 'speed',
                'required' => true,
                'validators'    =>  array(
                    array(
                        'name'  =>  'Int'
                    )
                )
            ));
            $inputFilter->add(array(
                'name' => 'distance',
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