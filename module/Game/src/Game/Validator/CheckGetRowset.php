<?php

namespace Game\Validator;

class CheckGetRowset extends AbstractValidator 
{
    protected $options = array(
        'callback'  =>  null,
        'rowset'    =>  null,
    );
    
    public function isValid($value)
    {
        $rowset = $this->getFilter()->getSm()->get($this->options['rowset'].'\Rowset');
        if($this->options['rand']){
            $row = $rowset->getRand();
        }else{
            $row = $rowset->getBy($this->options['key'], $value);
        }
        if(!$row){
            $this->error(self::OBJECT_NO_FOUND, $value);
            return false;
        }
        $this->setResult($row);
        return true;
    }
}