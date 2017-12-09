<?php

namespace Game\Validator\Facebook;

use Game\Validator\AbstractValidator;

class ShopPrice extends AbstractValidator 
{
    public function isValid($value)
    {
        $shopRow = $this->getFilter()->getShopRow();
        $count = $this->getFilter()->getCount();
        $price = floatval($shopRow->price) * $count;
        if($price == floatval($value)){
            return true;
        }
        $this->error(self::FACEBOOK_INVALID_PRICE);
        return false;
    }
}