<?php

namespace Game\Model\User\Attr;

use Ap\Model\Rowset AS Prototype;

class Rowset extends Prototype
{
    public function toArrayForApi($recursive = true)
    {
        $data = array();
        foreach($this->getItems() AS $row){
            $value = $row->getAttrRow()->filterValue($row->value);
            $data[$row->attr_code] = array(
                'value'     =>  $value,
                'datatype'  =>  $row->getAttrRow()->datatype
            );
        }
        return $data;
    }
}