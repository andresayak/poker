<?php

namespace Game\Validator;

class ObjectListShop extends AbstractValidator 
{
    protected $options = array(
        'type'    => null, 
    );
    protected $types = array(
        'transport', 'attack', 'spy'
    );
    
    public function isValid($value)
    {
        $cityRow = $this->getFilter()->getCityRow();
        $cityTable = $this->getFilter()->getSm()->get('City\Table');
        $cityTable->updateResources($cityRow);
        
        $shopRowset = $this->getFilter()->getSm()->get('Shop\Rowset');
        $needGems = 0;
        $itemCodes = $this->getFilter()->getItemCodes();
        foreach($this->getFilter()->getShopIds() AS $id) {
            if (!$shopRow = $shopRowset->getBy('id', $id)) {
                $this->error(self::OBJECT_NO_FOUND, $id);
                return false;
            }
            if($shopRow->isBuyGem()){
                $this->error(self::ORDER_HAVE_TYPE_GEMS, $id);
                return false;
            }
            foreach($shopRow->getObjectRowset()->getItems() AS $shopObjectRow){
                if($shopObjectRow->getObjectRow()->type != 'item' 
                    or $shopObjectRow->getObjectRow()->{'usable_in_'.$this->options['type']} == 'off'
                ){
                    $this->error(self::ORDER_HAVE_NO_USABLE_OBJECT, $id);
                    return false;
                }
                if(in_array($shopObjectRow->object_code, $itemCodes)){
                    $this->error(self::ORDER_HAVE_OBJECT_FROM_ITEMS, $id);
                    return false;
                }
                
            }
            $needGems+= $shopRow->money;
            
        }
        $haveCount = 0;
        if ($cityObjectRow = $cityRow->getObjectByCode('gem')){
            $cityObjectRow->blockForUpdate();
            $haveCount = $cityObjectRow->count;
        }
        if($haveCount < $needGems) {
            $this->error(self::OBJECT_NOT_ENOUGH_COUNT, 'gem');
            return false;
        }
        return true;
    }
}