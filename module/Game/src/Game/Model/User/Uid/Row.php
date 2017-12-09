<?php

namespace Game\Model\User\Uid;

use Ap\Model\Row as Prototype;

class Row extends Prototype
{
    protected $_user_row;
    
    public function getUserRow()
    {
        if($this->_user_row === null){
            $this->_user_row = $this->getSm()->get('User\Table')->fetchBy('id', $this->user_id);
        }
        return $this->_user_row;
    }
    
    public function setUserRow(UserRow $userRow)
    {
        $this->_user_row = $userRow;
        $this->user_id = $userRow->id;
        return $this;
    }
}