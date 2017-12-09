<?php

namespace Game\Model\Lib\Attribute;
use Zend\Stdlib\Hydrator\Strategy\ClosureStrategy;

trait ValueProvider {

    public function getHydrator()
    {
        if($this->_hydrator === null){
            $this->_hydrator = new \Ap\Model\HydratorObjectProperty();
            $createStrategy = function($type){
                return new ClosureStrategy(function($value){
                    return $value;
                }, function($value, $data) use($type){
                    if ($this->getAttributeRow()->datatype == $type) {
                        if ($type == 'int')
                            return (int) $value;
                        if ($type == 'float')
                            return (float) $value;
                        if ($type == 'bool') {
                            var_dump($value);
                            return (bool) $value;
                        }
                        if ($type == 'select') {
                            return null;
                        }
                    }
                    return null;
                });
            };
            $this->_hydrator->addStrategy('value_int', $createStrategy('int'));
            $this->_hydrator->addStrategy('value_float', $createStrategy('float'));
            $this->_hydrator->addStrategy('value_select', $createStrategy('select'));
            $this->_hydrator->addStrategy('value_bool', $createStrategy('bool'));
        }
        return $this->_hydrator;
    }
    
    public function getInputFilter()
    {
        if($this->_inputFilter === null){
            $inputFilter = new \Ap\InputFilter\InputFilter;
            $inputFilter->add(array(
                'name' => 'attribute_code',
                'required' => true,
            ));
            $inputFilter->add(array(
                'name' => 'value_int',
                'required' => false,
            ));
            $inputFilter->add(array(
                'name' => 'value_float',
                'required' => false,
            ));
            $inputFilter->add(array(
                'name' => 'value_select',
                'required' => false,
            ));
            $inputFilter->add(array(
                'name' => 'value_bool',
                'required' => false,
            ));
            $this->_inputFilter = $inputFilter;
        }
        return $this->_inputFilter;
    }
    
    public function getValue()
    {
        return $this->{'value_'.$this->getAttributeRow()->datatype};
    }
    
    public function getStrValue()
    {
        $value = $this->getValue();
        if($this->getAttributeRow()->datatype == 'bool'){
            return ($value)?'Yes':'No';
        }
        return $value;
    }
}
