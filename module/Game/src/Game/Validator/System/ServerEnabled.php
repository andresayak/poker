<?php

namespace Game\Validator\System;

use Game\Validator\AbstractValidator;

class ServerEnabled extends AbstractValidator 
{
    public function isValid($value)
    {
        $config = $this->getFilter()->getSm()->get('config');
        $result = ($config['server']['status'] === true);
        if(!$result){
            $this->error(self::SERVER_DISABLED);
            return false;
        }
        return true;
    }
}