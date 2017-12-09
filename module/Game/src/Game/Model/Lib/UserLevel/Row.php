<?php

namespace Game\Model\Lib\UserLevel;

use Ap\Model\Row as Prototype;

class Row extends Prototype
{
    protected $_inputFilter;
    
    public function getInputFilter()
    {
        if($this->_inputFilter === null){
            $inputFilter = new \Ap\InputFilter\InputFilter;
            $inputFilter->add(array(
                'name' => 'level',
                'required' => true,
                'filters' => array(
                    array('name' => 'StripTags'),
                    array('name' => 'StringTrim'),
                ),
                'validators' => array(
                    array(
                        'name' => 'not_empty',
                    ),
                    new \Zend\Validator\Db\NoRecordExists(
                        array(
                            'adapter'   =>  $this->getTable()->getTableGateway()->getAdapter(),
                            'table'     =>  $this->getTable()->getName(),
                            'field'     =>  'level',
                            'exclude'   =>  (($this->level)?array(
                                'field' => 'level',
                                'value' => $this->level
                            ):null)
                        )
                    )
                )
            ));

            $inputFilter->add(array(
                'name' => 'exp',
                'required' => true,
                'validators' => array(
                    array(
                        'name' => 'not_empty',
                    ),
                ),
            ));
            $this->_inputFilter = $inputFilter;
        }
        return $this->_inputFilter;
    }
}