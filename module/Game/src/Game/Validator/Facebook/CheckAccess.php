<?php

namespace Game\Validator\Facebook;

use Game\Validator\AbstractValidator;

class CheckAccess extends AbstractValidator 
{
    public function isValid($value)
    {
        $access_token =  $this->getFilter()->getValue('accessToken');
        $config = $this->getFilter()->getSm()->get('config');
        if(!$access_token){
            $this->error(self::OTHER_ERROR, 'not set facebook_access_token');
            return  false;
        }
        $command = 'curl -i -X GET '.' "https://graph.facebook.com/'.$config['facebook']['ver'].'/me?fields=id,name,link&access_token='.$access_token.'"';
        $out = exec($command);
        $data = json_decode($out, true);
        if(!count($data)){
            $this->error(self::OTHER_ERROR, 'facebook response is empty');
            return false;
        }
        if($data['id'] != $value){
            $this->error(self::OTHER_ERROR, $value.'/not set facebook_access_token');
            return false;
        }
        $command = 'curl -i -X GET '
            .' "https://graph.facebook.com/'.$config['facebook']['ver'].'/'.$value.'?fields=name%2Cid%2Clink&access_token='.$access_token.'"';
        $out = exec($command);
        $data = json_decode($out, true);
        $this->getFilter()->setFacebookResponse($data)
            ->setFacebookLang((isset($data['locale']))?$data['locale']:'en')
            ->setFacebookName($data['name']);
        return true;
    }
}