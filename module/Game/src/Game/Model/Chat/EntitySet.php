<?php

namespace Game\Model\Chat;

use Ap\MongoDb\EntitySet AS Prototype;

class EntitySet extends Prototype
{
    public function toArrayForApi($recursive = true, $cacheData = false)
    {
        $data = array();
        foreach($this AS $row){
            if($row->getUserRow() and $row->getUserRow()->ban_chat_status == 'off'){
                $data[] = $row->toArray();
            }
        }
        return $data;
    }
}
