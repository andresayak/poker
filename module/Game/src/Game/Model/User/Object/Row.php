<?php

namespace Game\Model\User\Object;

use Game\Model\User\Row AS UserRow;

use Ap\Model\Row as Prototype;
use Game\Model\ObjectCountInterface;
use Game\Model\Lib\Object\Row AS ObjectRow;

class Row extends Prototype
{
    protected $_object_row, $_user_row;
    
    public function getObjectRow()
    {
        if(null === $this->_object_row){
            $this->_object_row = $this->getSm()->get('Lib/Object/Table')->getRowset()->getBy('code', $this->object_code);
        }
        return $this->_object_row;
    }
    
    public function setObjectRow(ObjectRow $objectRow)
    {
        $this->_object_row = $objectRow;
        $this->object_code = $objectRow->code;
        return $this;
    }
    
    public function setUserRow(UserRow $userRow)
    {
        $this->_user_row = $userRow;
        $this->user_id = $userRow->id;
        return $this;
    }
    
    public function getCount()
    {
        return $this->count;
    }
}