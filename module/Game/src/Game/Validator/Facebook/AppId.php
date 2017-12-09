<?php

namespace Game\Validator\Facebook;

use Game\Validator\AbstractValidator;

class AppId extends AbstractValidator 
{
    public function isValid($value)
    {
        $config = $this->getFilter()->getSm()->get('config');
        if((int)$config['facebook']['app_id'] == (int)$value){
            return true;
        }
        $this->error(self::FACEBOOK_INVALID_ID);
        return false;
    }
}