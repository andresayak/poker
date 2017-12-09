<?php

namespace Game\Model\User\Message;

use Ap\Model\Row as Prototype;
use Game\Model\User\Row AS UserRow;

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
    
    public function getFavorite($user_id)
    {
        if($user_id == $this->user_id_to){
            return (bool)$this->favorite_to;
        }
        if($user_id == $this->user_id_from){
            return (bool)$this->favorite_from;
        }
        throw new \Exception('not found user_id');
    }
    
    public function isFavorite($user_id)
    {
        if($user_id == $this->user_id_to){
            return (bool)$this->favorite_to;
        }
        if($user_id == $this->user_id_from){
            return (bool)$this->favorite_from;
        }
        throw new \Exception('not found user_id');
    }
    
    public function isRemove($user_id)
    {
        if($user_id == $this->user_id_to){
            return !(bool)$this->remove_to;
        }
        if($user_id == $this->user_id_from){
            return !(bool)$this->remove_from;
        }
        throw new \Exception('not found user_id');
    }
    
    public function toArrayForAPi()
    {
        $data = $this->toArray();
        $data['username'] = $this->getUserFromRow()->username;
        return $data;
    }
}