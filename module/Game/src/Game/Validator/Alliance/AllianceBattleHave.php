<?php

namespace Game\Validator\Alliance;

use Game\Validator\AbstractValidator;

class AllianceBattleHave extends AbstractValidator 
{
    public function isValid($value)
    {
        $memberRow = $this->getFilter()->getUserRow()->getMemberRow();
        $regionRow = $this->getFilter()->getRegionRow();
        if($memberRow->alliance_id == $regionRow->alliance_id){
            $this->error(self::REGION_WAR_YOUR_CAPITAL);
            return false;
        }
        return true;
    }
}