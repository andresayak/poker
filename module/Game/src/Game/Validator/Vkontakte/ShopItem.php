<?php

namespace Game\Validator\Vkontakte;

use Game\Validator\AbstractValidator;

class ShopItem extends AbstractValidator 
{
    public function isValid($value)
    {
        $data = explode('_', $value);
        if(count($data)==3){
            $count = (int)$data[1];
            $shop_id = (int)$data[0];
            $city_id = (int)$data[2];
            $shopRow = $this->getFilter()->getSm()->get('Shop/Table')->fetchBy('id', $shop_id);
            $cityRow = $this->getFilter()->getSm()->get('City/Table')->fetchBy('id', $city_id);
            if($cityRow and $shopRow){
                $this->getFilter()->setShopRow($shopRow)
                    ->setCityRow($cityRow)
                    ->setCount($count);
                return true;
            }
        }
        return false;
    }
}