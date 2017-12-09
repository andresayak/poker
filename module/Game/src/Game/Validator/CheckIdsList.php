<?php

namespace Game\Validator;

class CheckIdsList extends AbstractValidator 
{
    protected $options = array(
        'callback'  =>  null,
    );
    
    public function isValid($value)
    {
        if(!preg_match('/^((\d+)\;?)+$/i', $value)){
            $this->error(self::IDS_LIST_INVALID);
            return false;
        }
        
        $list = array();
        foreach(array_filter(explode(';', $value)) AS $id){
            if((string) (int) $id !== (string) $id or (int) $id <= 0){
                $this->error(self::IDS_LIST_INVALID);
                return false;
            }
            $list[] = (int) $id;
        }
        $this->setResult($list);
        return true;
    }
}