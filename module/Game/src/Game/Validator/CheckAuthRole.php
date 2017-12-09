<?php

namespace Game\Validator;

class CheckAuthRole extends AbstractValidator 
{
    protected $options = array(
        'type'    => null, 
    );
    
    public function setRole($role)
    {
        $this->options['role'] = $role;
        return $this;
    }
    
    public function isValid($value)
    {
        $userRow = $this->getFilter()->getSm()->get('Auth\Service')->getUserRow();
        if(!$userRow or $this->_isValidRole($value)){
            $this->error(self::ACCESS_DENIED);
            return false;
        }
        return true;
    }
    protected function _isValidRole($value)
    {
        if(is_string($this->options['role'])){
            return ($value == $this->options['role']);
        }elseif(is_array($this->options['role'])){
            return (in_array($value, $this->options['role']));
        }
        return false;
    }
}