<?php

namespace Game\Model\System\Server;

use Ap\Model\Row as Prototype;

class Row extends Prototype
{
    protected $_inputFilter;
    public function getInputFilter()
    {
        if($this->_inputFilter === null){
            $inputFilter = new \Ap\InputFilter\InputFilter;
            $inputFilter->add(array(
                'name' => 'code',
                'required' => false,
                'filters' => array(
                    array('name' => 'StripTags'),
                    array('name' => 'StringTrim'),
                ),
            ));

            $inputFilter->add(array(
                'name' => 'ip_external_address',
                'required' => true,
            ));

            $inputFilter->add(array(
                'name' => 'ip_network_address',
                'required' => true,
            ));
            
            $inputFilter->add(array(
                'name' => 'host',
                'required' => true,
            ));
            $inputFilter->add(array(
                'name' => 'socket',
                'required' => true,
            ));
            $this->_inputFilter = $inputFilter;
        }
        return $this->_inputFilter;
    }
    
    public function toArrayForApi()
    {
        return array(
            'code'      =>  $this->code,
            'socket'    =>  $this->socket,
            'host'      =>  $this->host
        );
    }
}