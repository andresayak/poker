<?php

namespace Ap\Provider;

use Zend\Stdlib\Hydrator\Strategy\ClosureStrategy;

trait Depend {

    protected $_hydrator;
    
    public function getHydrator()
    {
        if($this->_hydrator === null){
            $this->_hydrator = new \Ap\Model\HydratorObjectProperty();
            $this->_hydrator->addStrategy('depend_code', new ClosureStrategy(function($value){
                return $value;
            }, function($value, $data){
                return $data[$data['type'].'_code'];
            }));
        }
        return $this->_hydrator;
    }
    
    public function getDependRow() 
    {
        if($this->_depend_row === null){
            $this->_depend_row = $this->getSm()->get('Lib\\'.ucfirst($this->type).'\Level\Table')
                ->fetchByArray(array(
                    $this->type.'_code' =>  $this->depend_code,
                    'level' =>  $this->level
                ));
        }
        return $this->_depend_row;
    }
    
    public function getInputFilter()
    {
        if($this->_inputFilter === null){
            $inputFilter = new \Ap\InputFilter\InputFilter;
            $inputFilter->add(array(
                'name' => 'depend_code',
                'required' => true,
                'validators'    =>  array(
                    array(
                        'name'  =>  'Int'
                    )
                )
            ));
            $inputFilter->add(array(
                'name' => 'level',
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
