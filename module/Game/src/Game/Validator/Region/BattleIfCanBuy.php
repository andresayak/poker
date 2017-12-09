<?php

namespace Game\Validator\Region;

use Game\Validator\AbstractValidator;

class BattleIfCanBuy extends AbstractValidator 
{
    public function isValid($value)
    {
        $regionRow = $this->getFilter()->getRegionRow();
        $battleRow = $regionRow->getBattleRow();
        $memberRow = $this->getFilter()->getUserRow()->getMemberRow();
        if($memberRow->alliance_id == $regionRow->alliance_id){
            $this->error(self::REGION_WAR_YOUR_CAPITAL);
            return false;
        }
        if($memberRow->alliance_id == $battleRow->alliance_id){
            $this->error(self::REGION_WAR_YOUR_BATTLE);
            return false;
        }
        return true;
    }
}