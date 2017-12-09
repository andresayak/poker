<?php

namespace Game\Model\Lib\Resource;

use Ap\Model\Row as Prototype;

class Row extends Prototype
{
    protected $_inputFilter;
    protected $_inside_rowset;
    
    public function getInsideRowset()
    {
        if($this->_inside_rowset === null){
            $this->_inside_rowset = $this->getSm()->get('Lib\Resource\Inside\Table')->fetchAllBy('resource_code', $this->code);
        }
        return $this->_inside_rowset;
    }

    public function getInputFilter()
    {
        if($this->_inputFilter === null){
            $inputFilter = new \Ap\InputFilter\InputFilter;
            $inputFilter->add(array(
                'name' => 'code',
                'required' => true,
                'filters'   =>  array(
                    array(
                        'name'=>'StringTrim'
                    ),
                )
            ));
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
            $inputFilter->add(array(
                'name' => 'type',
                'required' => true,
            ));
            $inputFilter->add(array(
                'name' => 'default_count',
                'required' => false,
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
                'name' => 'size',
                'required' => true,
            ));
            $this->_inputFilter = $inputFilter;
        }
        return $this->_inputFilter;
    }
    
    public function isCanInBox()
    {
        return $this->type != 'box';
    }
}