<?php

namespace Game\Model\Lib\Resource\Inside;

use Ap\Model\Row as Prototype;

class Row extends Prototype
{
    use \Game\Model\Lib\Resource\Provider;
    
    protected $_inputFilter;
    
    public function getInputFilter()
    {
        if($this->_inputFilter === null){
            $inputFilter = new \Ap\InputFilter\InputFilter;
            $inputFilter->add(array(
                'name' => 'depend_code',
                'required' => true,
            ));
            $inputFilter->add(array(
                'name' => 'count',
                'required' => true,
                'filters'   =>  array(
                    array(
                        'name'=>'ToNull'
                    )
                ),
                'validators'    =>  array(
                    array(
                        'name'  =>  'Int'
                    )
                )
            ));
            $inputFilter->add(array(
                'name'      =>  'probability',
                'required'  =>  true,
            ));
            $inputFilter->add(array(
                'name'      =>  'diff',
                'required'  =>  true,
            ));
            $this->_inputFilter = $inputFilter;
        }
        return $this->_inputFilter;
    }
}