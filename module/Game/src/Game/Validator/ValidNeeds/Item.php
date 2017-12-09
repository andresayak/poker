<?php

namespace Game\Validator\ValidNeeds;

use Game\Validator\AbstractValidator;

class Item extends AbstractValidator 
{
    protected $options = array(
        'object_code'   =>  null, 
        'count'         =>  null
    );
    
    public function isValid($value)
    {
        $count = (int) ($this->options['count'])? $this->options['count'] : $value;
        $userRow = $this->getFilter()->getUserRow();
        
        if($this->options['object_code']){
            $object_code = $this->options['object_code'];
        }else{
            $objectRow = $this->getFilter()->getObjectRow();
            $object_code = $objectRow->code;
        }
        $userObjectRow = $userRow->getObjectRowset()->getBy('object_code', $object_code);
        if($userObjectRow){
            $userObjectRow->blockForUpdate();
            if($userObjectRow->count >= $count){
                return true;
            }
        }
        $this->error(self::OBJECT_NOT_ENOUGH_COUNT, $object_code);
        return false;
    }
}