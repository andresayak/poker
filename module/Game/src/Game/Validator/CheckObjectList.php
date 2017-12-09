<?php

namespace Game\Validator;

class CheckObjectList extends AbstractValidator 
{
    public function isValid($value)
    {
        if(!preg_match('/^(([^\:]+)\:(\d+)\;?)+$/i', $value)){
            $this->error(self::OBJECT_LIST_INVALID);
            return false;
        }
        $list = array();
        foreach(array_filter(explode(';', $value)) AS $row){
            $values = explode(':', $row);
            if(count($values) != 2){
                $this->error(self::OBJECT_LIST_INVALID);
                return false;
            }
            list($code, $count) = $values;
            if((string) (int) $count !== (string) $count or (int) $count <= 0){
                $this->error(self::OBJECT_LIST_INVALID);
                return false;
            }
            if(isset($list[$code])){
                $this->error(self::OBJECT_LIST_INVALID);
                return false;
            }
            $list[$code] = (int) $count;
        }
        
        $this->getFilter()->setList($list);
        return true;
    }
}