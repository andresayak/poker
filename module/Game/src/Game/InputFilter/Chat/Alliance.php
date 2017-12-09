<?php

namespace Game\InputFilter\Chat;

use Game\InputFilter\InputFilter;

class Alliance extends InputFilter
{
    public function __construct($sm)
    {
        $this->_sm = $sm;
        
        $this->add(array(
            'name'     => 'message',
            'required' => true,
            'filters'=>array(
                array('name' => 'StripTags'),
                array('name' => 'StringTrim'),
            ),
            'validators' => array(
                array(
                    'name' => 'not_empty',
                ),
                array(
                    'name' => 'StringLength',
                    'options' => array('max' => 255)
                ),
                array(
                    'name'  =>  'Ap\Validator\Callback',
                    'options'   =>  array(
                        'callback'  =>  array($this, 'checkAccess'),
                        'messages'  =>  array('callbackValue'=>'Access denied')
                    ),
                    'break_chain_on_failure' => true
                )
            )
        ));
    }
    
    public function checkAccess()
    {
        return ($this->getUserRow()->getMemberRow());
    }
    
    public function finish()
    {
        $service = $this->getSm()->get('Chat\Service');
        $alliance_id = $this->getUserRow()->getMemberRow()->alliance_id;
        
        $data = array(
            'type'          =>  'alliance',
            'message'       =>  $this->get('message')->getValue(),
            'time_send'     =>  time()
        );
        $this->getUserRow()->addInfo($data);
        
        $service->addToAlliance($data, $alliance_id);
        
        //$this->getUserRow()->getQuestManager()->event('alliance_chat');
    }
}