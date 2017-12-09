<?php

namespace Game\Validator;

class CheckForgotKey extends AbstractValidator 
{
    public function isValid($value)
    {
        if($value != $this->getFilter()->getUserRow()->forgot_key
            or $this->getFilter()->getUserRow()->forgot_status !=='on'
        ){
            $this->error(self::FORGOT_KEY_INVALID);
            return false;
        }
        return true;
    }
}