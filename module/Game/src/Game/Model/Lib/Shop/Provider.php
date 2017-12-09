<?php

namespace Game\Model\Lib\Shop;

trait Provider {

    use \Game\Provider\ProvidesServiceManager;
    
    protected $_shop_row;

    public function getShopRow() 
    {
        if($this->_shop_row === null){
            $this->_shop_row = $this->getSm()->get('Lib\Shop\Table')->fetchBy('code', $this->shop_code);
        }
        return $this->_shop_row;
    }

    public function setShopRow(Row $shopRow) 
    {
        $this->_shop_row = $shopRow;
        return $this;
    }
}
