<?php

namespace Game\Model\System\Paylog;

use Ap\Model\Row as Prototype;

use Game\Model\Shop\Row AS ShopRow;
use Game\Model\Server\Row AS ServerRow;

class Row extends Prototype
{
    protected $_shop_row;
    
    public function setShopRow(ShopRow $shopRow)
    {
        $this->_shop_row = $shopRow;
        $this->shop_id = $shopRow->id;
        return $this;
    }
    
    public function getShopRow()
    {
        if(null === $this->_shop_row){
            $this->_shop_row = $this->getSm()->get('Shop\Table')->getRowset()->getBy('id', $this->shop_id);
        }
        return $this->_shop_row;
    }
}