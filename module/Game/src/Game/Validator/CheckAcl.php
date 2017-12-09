<?php

namespace Game\Validator;

class CheckAcl extends AbstractValidator 
{
    protected $options = array(
        'resource'  =>  null
    );
    
    public function setResource($resource)
    {
        $this->options['resource'] = $resource;
        return $this;
    }
    
    public function isValid($value)
    {
        $userRow = $this->getFilter()->getSm()->get('Auth\Service')->getUserRow();
        $aclService = $this->getFilter()->getSm()->get('Acl\Service');
        if($aclService->isAllowed($userRow->role, $this->options['resource'])){
            $this->error(self::ACCESS_DENIED);
            return false;
        }
        return true;
    }
}