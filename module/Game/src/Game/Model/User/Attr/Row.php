<?php

namespace Game\Model\User\Attr;

use Game\Model\Lib\Attr\AbstractValueRow as Prototype;
use Game\Model\User\Row AS UserRow;

class Row extends Prototype
{
    protected $_user_row;
    
    public function setUserRow(UserRow $userRow)
    {
        $this->_user_row = $userRow;
        $this->user_id = $userRow->id;
        return $this;
    }
    
    public function getUserRow()
    {
        if(null === $this->_user_row){
            $this->_user_row = $this->getSm()->get('User/Table')->fetchById($this->user_id);
        }
        return $this->_user_row;
    }
    public function getValue()
    {
        $value = $this->getAttrRow()->filterValue($this->value);
        return $value;
    }
}