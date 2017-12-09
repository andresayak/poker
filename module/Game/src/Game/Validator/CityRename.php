<?php

namespace Game\Validator;

class CityRename extends AbstractValidator 
{
    public function isValid($value)
    {
        if(preg_match('/^((player|city)\d*)$/i', $value)){
            $this->error(self::CITYNAME_INVALID);
            return false;
        }
        return true;
    }
}