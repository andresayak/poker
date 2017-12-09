<?php

namespace Game\Validator;

class ShopPromo extends AbstractValidator 
{
    
    public function isValid($value)
    {
        $shopRow = $this->getFilter()->getShopRow();
        $shopRowset = $this->getFilter()->getSm()->get('Shop\Rowset');
        if($shopRow->type != 'promo' and $shopRow->type != 'promo_gem'){
            return true;
        }
        if(!$shopRowset->isPromoActive($shopRow)) {
            $this->error(self::PROMO_NOT_ACTIVE);
            return false;
        }
        return true;
    }
}