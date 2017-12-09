<?php

namespace Game\Validator;

class CheckAccess extends AbstractValidator 
{
    
    public function isValid($value)
    {
        if(!$this->getFilter()->getUserRow()){
            $this->error(self::ACCESS_DENIED);
            return false;
        }
        return true;
    }
}