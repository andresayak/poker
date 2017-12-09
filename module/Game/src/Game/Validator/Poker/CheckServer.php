<?php

namespace Game\Validator\Poker;

use Game\Validator\AbstractValidator;

class CheckServer extends AbstractValidator 
{
    protected $options = array(
    );
    
    protected $messageTemplates = array(
        'invalid_server' =>  'invalid server',
    );
    public function isValid($position)
    {
        $gameRow = $this->getFilter()->getGameRow();
        $config = $this->getFilter()->getSm()->get('config');
        if($gameRow->server_code != $config['server_code']){
            $this->error('invalid_server');
            return false;
        }
        return true;
    }
}