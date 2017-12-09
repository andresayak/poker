<?php

namespace Game\Model\Shop\Object;

use Ap\Model\Row as Prototype;
use Game\Model\Lib\Object\Row AS ObjectRow;

class Row extends Prototype
{
    protected $_object_row, $_shop_row;

    public function getObjectRow()
    {
        if($this->_object_row === null){
            $this->_object_row = $this->getSm()->get('Lib\Object\Table')->getRowset()->getBy('code', $this->object_code);
        }
        return $this->_object_row;
    }
    
    public function setObjectRow(ObjectRow $objectRow)
    {
        $this->_object_row = $objectRow;
        $this->object_code = $objectRow->code;
        return $this;
    }
    
    public function getShopRow()
    {
        if($this->_shop_row === null){
            $this->_shop_row = $this->getSm()->get('Shop\Table')->getRowset()->getBy('id', $this->shop_id);
        }
        return $this->_shop_row;
    }
    
    public function getCount()
    {
        return round_down($this->count, 0);
    }
}