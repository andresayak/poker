<?php

namespace Game\Validator;

class CheckCodesList extends AbstractValidator 
{
    protected $options = array(
        'callback'  =>  null,
    );
    
    public function isValid($value)
    {
        if(!preg_match('/^((\w+)\;?)+$/i', $value)){
            $this->error(self::OBJECT_LIST_INVALID);
            return false;
        }
        
        $list = array();
        foreach(array_filter(explode(';', $value)) AS $code){
            if((string) (int) $code == (string) $code){
                $this->error(self::IDS_LIST_INVALID);
                return false;
            }
            $list[] = (string) $code;
        }
        $this->setResult($list);
        return true;
    }
}