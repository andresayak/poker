<?php

namespace Game\Validator;

class ObjectType extends AbstractValidator 
{
    protected $options = array(
        'type'    => null, 
    );
    
    public function setType($type)
    {
        $this->options['type'] = $type;
        return $this;
    }
    
    public function isValid($value)
    {
        $objectRow = $this->getFilter()->getObjectRow();
        
        if($objectRow->type !== $this->options['type']){
            $this->error(self::OBJECT_TYPE_INVALID, $objectRow->type);
            return false;
        }
        return true;
    }
}