<?php

namespace Game\Validator\Mailru;

use Game\Validator\AbstractValidator;

class CheckAccess extends AbstractValidator 
{
    public function isValid($value)
    {
        $access_token =  $this->getFilter()->getValue('mm_auth_key');
        if(!$access_token){
            $this->error(self::OTHER_ERROR, 'not set mm_auth_key');
            return  false;
        }
        
        $session_key = $this->getFilter()->getValue('mm_session_key');
        if(!$session_key){
            $this->error(self::OTHER_ERROR, 'not set mm_session_key');
            return  false;
        }
        $config = $this->getFilter()->getSm()->get('config');
        if(!isset($config['mailru'])){
            $this->error(self::OTHER_ERROR, 'not set mailru config');
            return  false;
        }
        $params =  $this->getFilter()->getValue('mm_params');
        $sig = md5($params. $config['mailru']['key']);
        if($sig == $access_token){
            $sigNew = md5('app_id='.$config['mailru']['app_id'].'method=users.getInfosecure=1session_key='.$session_key.$config['mailru']['key']);
            $url = 'http://www.appsmail.ru/platform/api?method=users.getInfo'
                .'&secure=1'
                .'&app_id='.$config['mailru']['app_id']
                .'&session_key='.$session_key
                .'&sig='.$sigNew;
            $command = 'curl "'.$url.'"';

            $out = exec($command);
            $data = json_decode($out, true);
            if(isset($data[0])){
                $this->getFilter()->setMailruResponse($data[0]);
                return true;
            }
        }
        return  false;
    }
}