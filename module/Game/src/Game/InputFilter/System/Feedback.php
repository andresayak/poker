<?php

namespace Game\InputFilter\System;

use Game\InputFilter\InputFilter;
use Game\Model\System\Mailer AS SystemMailer;

class Feedback extends InputFilter
{
    public function __construct($sm)
    {
        $this->_sm = $sm;
        $this->add(array(
            'name' => 'subject',
            'required' => true,
            'filters' => array(
                array('name' => 'StripTags'),
                array('name' => 'StringTrim'),
            ),
            'validators' => array(
                array(
                    'name' => 'not_empty',
                    'break_chain_on_failure' => true
                ),
                array(
                    'name' => 'StringLength',
                    'options' => array('max' => 256)
                )
        )));
        
        $this->add(array(
            'name' => 'message',
            'required' => true,
            'filters' => array(
                array('name' => 'StripTags'),
                array('name' => 'StringTrim'),
            ),
            'validators' => array(
                array(
                    'name' => 'not_empty',
                    'break_chain_on_failure' => true
                ),
                array(
                    'name' => 'StringLength',
                    'options' => array('max' => 4000)
                )
        )));
        
        $this->add(array(
            'name' => 'email',
            'required' => true,
            'filters' => array(
                array('name' => 'StripTags'),
                array('name' => 'StringTrim'),
            ),
            'validators' => array(
                array(
                    'name' => 'not_empty',
                    'break_chain_on_failure' => true
                ),
                array(
                    'name' => 'EmailAddress',
                    'break_chain_on_failure'=>true
                ),
                array(
                    'name' => 'StringLength',
                    'options' => array('max' => 256)
                )
        )));
        
        $this->add(array(
            'name' => 'name',
            'required' => true,
            'filters' => array(
                array('name' => 'StripTags'),
                array('name' => 'StringTrim'),
            ),
            'validators' => array(
                array(
                    'name' => 'not_empty',
                    'break_chain_on_failure' => true
                ),
                array(
                    'name' => 'StringLength',
                    'options' => array('max' => 256)
                )
        )));
    }
    
    public function getSm()
    {
        return $this->_sm;
    }
    
    public function finish()
    {
        $feedbackTable = $this->getSm()->get('System\Feedback\Table');
        $feedbackRow = $feedbackTable->createRow(array(
            'email'         => $this->getValue('email'),
            'subject'       => $this->getValue('subject'),
            'message'       => $this->getValue('message'),
            'name'          => $this->getValue('name'),
            'ip_address'    => ip2long(IP_ADDRESS),
            'user_id'       => $this->getUserRow()->id,
            'time_add'      => time()
        ));
        $feedbackTable->add($feedbackRow);
        
        SystemMailer::feedback($feedbackRow, $this->getSm());
    }
}