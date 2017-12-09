<?php

namespace Game\Validator\Alliance;

use Game\Validator\AbstractValidator;

class UserIsLider extends AbstractValidator 
{
    public function isValid($value)
    {
        $memberRow = $this->getFilter()->getUserRow()->getMemberRow();
        if(!$memberRow and $memberRow->role == 'admin'){
            $this->error(self::USER_NOT_LIDER_IN_ALLIANCE);
            return false;
        }
        return true;
    }
}