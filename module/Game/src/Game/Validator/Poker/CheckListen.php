<?php

namespace Game\Validator\Poker;

use Game\Validator\AbstractValidator;

class CheckListen extends AbstractValidator 
{
    protected $options = array(
    );
    
    protected $messageTemplates = array(
        'not_listen'    =>  'Not listen game'
    );
    public function isValid($value)
    {
        if($this->getFilter()->getGameRow()->isListen($this->getFilter()->getUserRow())){
            return true;
        }
        $this->error('not_listen');
        return false;
    }
}