<?php

namespace Game\Validator\Alliance;

use Game\Validator\AbstractValidator;

class HaveGempsToAdd extends AbstractValidator 
{
    public function isValid($value)
    {
        $price = $value;
        $allianceRow = $this->getFilter()->getUserRow()->getMemberRow()->getAllianceRow();
        if($price < REGION_BATTLE_PRICE){
            $this->error(self::REGION_WAR_MIN_PRICE, 'gem');
            return false;
        }
        $limit = 0;
        if($objectRow = $allianceRow->getObjectRowset()->getBy('object_code', 'gem')){
            $objectRow->blockForUpdate();
            $limit = $objectRow->count;
        }
        if($price > $limit){
            $this->error(self::OBJECT_NOT_ENOUGH_COUNT, 'gem');
            return false;
        }
        return true;
    }
}