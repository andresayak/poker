<?php

namespace Game\Validator\Vkontakte;

use Game\Validator\AbstractValidator;

class AppId extends AbstractValidator 
{
    public function isValid($value)
    {
        $config = $this->getFilter()->getSm()->get('config');
        return (int)$config['vkontakte']['app_id'] == (int)$value;
    }
}