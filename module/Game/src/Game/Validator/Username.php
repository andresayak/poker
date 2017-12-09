<?php

namespace Game\Validator;

class Username extends AbstractValidator 
{
    public function isValid($value)
    {
        if(preg_match('/^(player\d*)$/i', $value)){
            $this->error(self::USERNAME_INVALID);
            return false;
        }
        if(preg_match('/^[\w\.\d]*$/i', $value)){
            return true;
        }
        $this->error(self::USERNAME_INVALID);
        return false;
    }
}