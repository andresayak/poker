<?php

namespace Game\Validator\Vkontakte;

use Game\Validator\AbstractValidator;

class CheckAccess extends AbstractValidator 
{
    public function isValid($value)
    {
        $config = $this->getFilter()->getSm()->get('config');
        $options = $config['vkontakte'];
        
        $auth_key = md5($options['app_id']  . '_' . $this->getFilter()->getValue('vk_uid') . '_' . $options['key']);
        if($this->getFilter()->getValue('vk_auth_key')!= $auth_key){
            return false;
        }
        /*
        $command = 'curl "https://oauth.vk.com/access_token?client_id=' . $options['app_id'] . '&client_secret=' . $options['key'] . '&v=5.27&grant_type=client_credentials"';
        $out = exec($command);
        $data = json_decode($out, true);
        
        $access_token = $data['access_token'];
        */
        
        return true;
    }
}