<?php

namespace Game\Model\User\Interaction;

use Ap\Model\Row as Prototype;

class Row extends Prototype
{
    protected $_user_from_row, $_user_to_row;

    public function getUserFromRow()
    {
        if($this->_user_from_row === null){
            $this->_user_from_row = $this->getSm()->get('User\Table')->fetchById($this->user_id_from);
        }
        return $this->_user_from_row;
    }
    
    public function setUserFromRow(UserRow $userRow)
    {
        $this->_user_from_row = $userRow;
        $this->user_id_from = $userRow->id;
        return $this;
    }
    
    public function getUserToRow()
    {
        if($this->_user_to_row === null){
            $this->_user_to_row = $this->getSm()->get('User\Table')->fetchById($this->user_id_to);
        }
        return $this->_user_to_row;
    }
    
    public function setUserToRow(UserRow $userRow)
    {
        $this->_user_to_row = $userRow;
        $this->user_id_to = $userRow->id;
        return $this;
    }
}