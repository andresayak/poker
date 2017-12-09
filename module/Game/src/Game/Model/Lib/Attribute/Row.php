<?php

namespace Game\Model\Lib\Attribute;

use Ap\Model\Row as Prototype;
use Zend\Stdlib\Hydrator\Strategy\ClosureStrategy;

class Row extends Prototype
{
    protected $_option_rowset;
    
    public function getHydrator()
    {
        if($this->_hydrator === null){
            $this->_hydrator = new \Ap\Model\HydratorObjectProperty();
            $typeFilter = new ClosureStrategy(function($value){
                return $value;
            }, function($value, $data){
                if(isset($data['datatype'])){
                    if($data['datatype'] == 'int'){
                        return (int)$value;
                    }
                    if($data['datatype'] == 'float'){
                        return (float)$value;
                    }
                    if($data['datatype'] == 'bool'){
                        return (bool)$value;
                    }
                    if($data['datatype'] == 'select'){
                        return null;
                    }
                }
                return $value;
            });
            $this->_hydrator->addStrategy('max_value', $typeFilter);
            $this->_hydrator->addStrategy('min_value', $typeFilter);
            $this->_hydrator->addStrategy('default_value', $typeFilter);
            $this->_hydrator->addStrategy('default_id', new ClosureStrategy(function($value){
                return $value;
            }, function($value, $data){
                if(isset($data['datatype'])){
                    if($data['datatype'] != 'select'){
                        return null;
                    }
                    $optionRow = $this->getOptionRowset()->getBy('id', $value);
                    if(!$optionRow){
                        return null;
                    }
                    return $optionRow->id;
                }
                return $value;
            }));
        }
        return $this->_hydrator;
    }
    
    public function getInputFilter()
    {
        if($this->_inputFilter === null){
            $inputFilter = new \Ap\InputFilter\InputFilter;
            $inputFilter->add(array(
                'name' => 'code',
                'required' => true,
            ));
            $inputFilter->add(array(
                'name' => 'title',
                'required' => true,
            ));
            $inputFilter->add(array(
                'name' => 'filename',
                'required' => true,
            ));
            $inputFilter->add(array(
                'name' => 'datatype',
                'required' => true,
            ));
            $inputFilter->add(array(
                'name' => 'assignation',
                'required' => true,
            ));
            $inputFilter->add(array(
                'name' => 'typechange',
                'required' => true,
            ));
            $inputFilter->add(array(
                'name' => 'max_value',
                'required' => false,
            ));
            $inputFilter->add(array(
                'name' => 'min_value',
                'required' => false,
            ));
            $inputFilter->add(array(
                'name' => 'default_value',
                'required' => false,
            ));
            $inputFilter->add(array(
                'name' => 'default_id',
                'required' => false,
            ));
            $this->_inputFilter = $inputFilter;
        }
        return $this->_inputFilter;
    }
    
    public function getOptionRowset()
    {
        if($this->_option_rowset === null){
            $this->_option_rowset = $this->getSm()->get('Lib\Attribute\Option\Table')->fetchAllBy('attribute_code', $this->code);
        }
        return $this->_option_rowset;
    }
    
    public function isAssignation($type)
    {
        return ($this->assignation == $type);
    }
}