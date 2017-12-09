<?php

namespace Game\Model\User\Notification;

use Ap\Model\Row as Prototype;
use Game\Model\User\Row AS UserRow;

class Row extends Prototype
{
    protected $_user_row, $_template_row;
    protected $_arguments;
    
    public function getUserRow()
    {
        if($this->_user_row === null){
            $this->_user_row = $this->getSm()->get('User\Table')->fetchById($this->user_id);
        }
        return $this->_user_row;
    }
    
    public function setUserRow(UserRow $userRow)
    {
        $this->_user_row = $userRow;
        $this->user_id = $userRow->id;
        return $this;
    }
    
    public function getTemplateRow()
    {
        if(null === $this->_template_row){
            $this->_template_row = $this->getSm()->get('Lib\Notification\Rowset')->getBy('code', $this->template);
        }
        return $this->_template_row;
    }
    
    public function getIcon()
    {
        return ($this->getTemplateRow())?$this->getTemplateRow()->icon:'--';
    }
    
    public function getArguments()
    {
        if($this->_arguments === null){
            $this->_arguments = unserialize($this->arguments);
        }
        return $this->_arguments;
    }

    public function setArguments($arguments)
    {
        $this->arguments = serialize($arguments);
        $this->_arguments = $arguments;
        return $this;
    }
    
    public function toArrayForAPi()
    {
        $data = $this->toArray();
        $data['arguments'] = $this->getArguments();
        return $data;
    }

}