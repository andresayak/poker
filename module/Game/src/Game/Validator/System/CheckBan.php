<?php

namespace Game\Validator\System;

use Game\Validator\AbstractValidator;

class CheckBan extends AbstractValidator 
{
    protected $options = array(
    );
    
    public function isValid($value)
    {
        $userRow = $this->getFilter()->getUserRow();
        if($userRow and $userRow->ban_status == 'on'){
            $this->error(self::BAN_STATUS);
            return false;
        }
        $config = $this->getFilter()->getSm()->get('config');
        $ban_ips = (isset($config['ip_bans']) and is_array($config['ip_bans']))?$config['ip_bans']:array();
        if(in_array(IP_ADDRESS, $ban_ips)){
            $this->error(self::BAN_STATUS);
            return false;
        }
        return true;
    }
}