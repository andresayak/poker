<?php

namespace Game\Validator\Alliance;

use Game\Validator\AbstractValidator;

class HaveGempsToBuy extends AbstractValidator 
{
    public function isValid($value)
    {
        $battleRow = $this->getFilter()->getRegionRow()->getBattleRow();
        $allianceRow = $this->getFilter()->getUserRow()->getMemberRow()->getAllianceRow();
        $price = $battleRow->getPrice();
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