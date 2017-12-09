<?php

namespace Game\Validator;

class CheckAuth extends AbstractValidator 
{
    public function isValid($value)
    {
        
        $authService = $this->getFilter()->getSm()->get('Auth\Service');
        if(!$authService->isUser()){
            $this->error(self::ACCESS_DENIED);
            return false;
        }
        $userRow = $authService->getUserRow();
        if(!$userRow){
            $this->error(self::ERROR_NO_RECORD_FOUND, print_r($authService->getAuthService()->getIdentity(), true));
            return false;
        }
        
        return true;
    }
}