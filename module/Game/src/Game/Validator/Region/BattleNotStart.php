<?php

namespace Game\Validator\Region;

use Game\Validator\AbstractValidator;

class BattleNotStart extends AbstractValidator 
{
    public function isValid($value)
    {
        $battleRow = $this->getFilter()->getRegionRow()->getBattleRow();
        if(!$battleRow){
            $this->error(self::REGION_WAR_NOT_CREATE);
            return false;
        }   
        if($battleRow->isStart()){
            $this->error(self::REGION_WAR_IS_START);
            return false;
        }
        return true;
    }
}