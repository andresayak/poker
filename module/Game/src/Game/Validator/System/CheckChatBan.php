<?php

namespace Game\Validator\System;

use Game\Validator\AbstractValidator;

class CheckChatBan extends AbstractValidator 
{
    protected $options = array(
    );
    
    public function isValid($value)
    {
        $userRow = $this->getFilter()->getUserRow();
        if($userRow and $userRow->getBanChatStatus() == 'on'){
            $this->error(self::BAN_STATUS);
            return false;
        }
        return true;
    }
}