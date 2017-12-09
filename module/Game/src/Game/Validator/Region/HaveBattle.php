<?php

namespace Game\Validator\Region;

use Game\Validator\AbstractValidator;

class HaveBattle extends AbstractValidator 
{
    public function isValid($value)
    {
        $regionRow = $this->getFilter()->getRegionRow();
        $regionRow->blockForUpdate();
        if($regionRow->status_war == 'on'){
            $this->error(self::REGION_WAR_NOT_END);
            return false;
        }
        return true;
    }
}