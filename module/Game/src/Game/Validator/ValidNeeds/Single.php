<?php

namespace Game\Validator\ValidNeeds;

use Game\Validator\AbstractValidator;

class Single extends AbstractValidator 
{
    public function isValid($value)
    {
        $count = (int) $value;
        $cityRow = $this->getFilter()->getCityRow();
        $objectRow = $this->getFilter()->getObjectRow();
        $cityObjectRow = $cityRow->getObjectByCode($objectRow->code);
        if($cityObjectRow){
            $cityObjectRow->blockForUpdate();
            if($cityObjectRow->count >= $count){
                return true;
            }
        }
        $this->error(self::OBJECT_NOT_ENOUGH_COUNT, $objectRow->code);
        return false;
    }
}