<?php

namespace Game\Model\User\Object;

use Ap\Model\Rowset as Prototype;
use Application\Model\User\Row AS UserRow;

class Rowset extends Prototype
{
    public function plus(Prototype $rowset, UserRow $userRow)
    {
        foreach ($rowset->getItems() AS $objectRow){
            $userRow->updateObjectRow($objectRow->object_code, $objectRow->getCount());
        }
        return $this;
    }
    
    public function minus(Prototype $rowset)
    {
        foreach ($rowset->getItems() AS $objectRow) {
            $userObjectRow = $this->getBy('object_code', $objectRow->object_code);
            if (!$userObjectRow) {
                throw new \Exception('userObjectRow not found(' . $objectRow->object_code . ')');
            }
            $userObjectRow->blockForUpdate();
            $userObjectRow->count-= $objectRow->getCount();
            $userObjectRow->save();
        }
        return $this;
    }
    
    public function toArrayForApi($recursive = true)
    {
        $data = array();
        foreach($this->getItems() AS $row){
            $data[] = array(
                'code'  =>  $row->object_code,
                'count' => $row->count,
            );
        }
        return $data;
    }
}