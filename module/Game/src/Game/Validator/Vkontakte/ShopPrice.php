<?php

namespace Game\Validator\Vkontakte;

use Game\Validator\AbstractValidator;

class ShopPrice extends AbstractValidator 
{
    public function isValid($value)
    {
        $shopRow = $this->getFilter()->getShopRow();
        $count = $this->getFilter()->getCount();
        $config = $this->getFilter()->getSm()->get('config');
        $price = (int)ceil($config['vkontakte']['price_rate']*$shopRow->price) * $count;
        return ($price == (int) $value);
    }
}