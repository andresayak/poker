<?php

namespace Game\Model\Shop\Object;

use Ap\Model\Rowset AS Prototype;

class Rowset extends Prototype
{
    public function toArrayForApi($recursive = true, $cacheData = false)
    {
        $data = array();
        foreach($this->getItems() AS $row){
            $data[$row->object_code] = $row->count;
        }
        return $data;
    }
    
}