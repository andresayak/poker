<?php

namespace Game\Validator;

class UidNotUsed extends AbstractValidator 
{
    protected $types = array(
        'default', 'fb', 'vk'
    );
    protected $options = array(
        'type'      => 'default', 
    );
    
    public function setType($type)
    {
        if(!in_array($type, $this->types)){
            throw new \Exception('Type invalid ['.$type.']');
        }
        $this->options['type'] = $type;
        return $this;
    }
    
    public function isValid($value)
    {
        $table = $this->getFilter()->getSm()->get('User\Uid\Table');
        $uidRow = $table->fetchByArray(array(
            'uid'       =>  $value,
            'auth_type' =>  $this->options['type']
        ), true);
        if(!$uidRow){
            return true;
        }
        $this->getFilter()->setUidRow($uidRow);
        return true;
    }
}