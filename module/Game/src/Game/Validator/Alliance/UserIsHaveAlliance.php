<?php

namespace Game\Validator\Alliance;

use Game\Validator\AbstractValidator;

class UserIsHaveAlliance extends AbstractValidator 
{
    public function isValid($value)
    {
        $memberRow = $this->getFilter()->getUserRow()->getMemberRow();
        if(!$memberRow){
            $this->error(self::USER_NOT_HAVE_ALLIANCE);
            return false;
        }
        return true;
    }
}