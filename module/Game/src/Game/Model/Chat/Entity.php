<?php

namespace Game\Model\Chat;

use Ap\MongoDb\Entity AS Prototype;

class Entity extends Prototype
{
    protected $_user_row, $_region_row;
    
    public function getUserRow()
    {
        if($this->_user_row === null){
            $this->_user_row = $this->getSm()->get('User\Table')->fetchBy('id', $this->user_id);
        }
        return $this->_user_row;
    }
    
    public function getRegionRow()
    {
        if($this->_region_row === null){
            $this->_region_row = $this->getSm()->get('Region\Table')->fetchBy('id', $this->region_id);
        }
        return $this->_region_row;
    }
}
