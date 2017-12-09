<?php

namespace Game\Validator;

class CheckFetchRow extends AbstractValidator 
{
    protected $options = array(
        'callback'  =>  null,
        'table'     =>  null,
        'key'       =>  'id'
    );
    
    public function isValid($value)
    {
        if(!$this->options['table']){
            throw new \Exception('table not set');
        }
        $table = $this->getFilter()->getSm()->get($this->options['table'].'\Table');
        $row = $table->fetchBy($this->options['key'], $value);
        if(!$row){
            $this->error(self::ERROR_NO_RECORD_FOUND);
            return false;
        }
        $this->setResult($row);
        return true;
    }
}