<?php

namespace Game\Model\Lib\Attribute;

use Ap\Model\Rowset as Prototype;

class Rowset extends Prototype
{
    public function toArrayForApi()
    {
        $data = array();
        foreach ($this->getItems() AS $row){
            $item = $row->toArray();
            if($row->datatype == 'select'){
                $item['optionList'] = $row->getOptionRowset()->toArrayForApi();
            }
            $data[] = $item;
        }
        return $data;
    }
}