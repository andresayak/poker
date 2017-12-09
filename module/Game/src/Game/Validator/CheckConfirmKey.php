<?php

namespace Game\Validator;

class CheckConfirmKey extends AbstractValidator 
{
    public function isValid($value)
    {
        if($value != $this->getFilter()->getUserRow()->confirm_key 
            or $this->getFilter()->getUserRow()->confirm_status !=='off'
        ){
            $this->error(self::CONFIRM_KEY_INVALID);
            return false;
        }
        return true;
    }
}