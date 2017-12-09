<?php

namespace Game\Validator\System;

use Game\Validator\AbstractValidator;

class Version extends AbstractValidator 
{
    public function isValid($value)
    {
        if($value != API_VER){
            $this->error(self::INVALID_VER);
            return false;
        }
        return true;
    }
}