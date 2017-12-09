<?php

namespace Game\Model\Lib\AllianceLevel;

use Ap\Model\Row as Prototype;

class Row extends Prototype
{
    protected $_inputFilter;
    
    public function getInputFilter()
    {
        if($this->_inputFilter === null){
            $inputFilter = new \Ap\InputFilter\InputFilter;
            $inputFilter->add(array(
                'name' => 'id',
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
                            'field'     =>  'id',
                            'exclude'   =>  (($this->id)?array(
                                'field' => 'id',
                                'value' => $this->id
                            ):null)
                        )
                    )
                )
            ));

            $inputFilter->add(array(
                'name' => 'title',
                'required' => true,
                'validators' => array(
                    array(
                        'name' => 'not_empty',
                    ),
                ),
            ));

            $inputFilter->add(array(
                'name' => 'score',
                'required' => true,
                'validators' => array(
                    array(
                        'name' => 'not_empty',
                    ),
                ),
            ));
            
            $inputFilter->add(array(
                'name' => 'member_count',
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