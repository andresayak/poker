<?php

namespace Game\InputFilter\Chat;

use Game\InputFilter\InputFilter;

class All extends InputFilter
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
                    'name'  =>  'checkAuth',
                    'break_chain_on_failure' => true
                ),
                array(
                    'name' => 'StringLength',
                    'options' => array('max' => 255)
                ),
                array(
                    'name'  =>  'checkChatBan',
                    'break_chain_on_failure' => true
                ),
            )
        ));
    }
    
    public function finish()
    {
        $service = $this->getSm()->get('Chat\Service');
        $color = 'default';
        $data = array(
            'type'      =>  'public',
            'moderator' =>  ($this->getUserRow()->role == 'moderator'),
            'color'     =>  $color,
            'message'   =>  $this->get('message')->getValue(),
            'time_send' =>  time()
        );
        $this->getUserRow()->addInfo($data);
        $service->addToPublic($data);
    }
}