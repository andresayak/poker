<?php

namespace Game\Validator;

class TransportEating extends AbstractValidator 
{
    public function isValid($value)
    {
        $cityRow = $this->getFilter()->getCityRow();
        
        $cityTable = $this->getFilter()->getSm()->get('City\Table');
        $cityTable->updateResources($cityRow);
        if((bool)$this->getFilter()->getValue('back')){
            $this->getFilter()->getTransportManager()->setFoodCost(0);
            return true;
        }
        $count = 0;
        foreach($this->getFilter()->getTransportManager()->getObjectRowset()->getItems() AS $objectRow){
            if($objectRow->getObjectRow()->type == 'unit'){
                $count+= $this->getEating($objectRow, $cityRow);
            }
        }
        $rate = $this->getFilter()->getTransportManager()->getTransportRow()->getAttrValue('rate_eating');
        $distance = $this->getFilter()->getTransportManager()->getDistance();
        $countEnd = $count * $rate * $distance * TRANSPORT_EATING_RATE;
        $limit = 0;
        if($cityObjectRow = $cityRow->getObjectByCode('food')){
            $cityObjectRow->blockForUpdate();
            $limit = $cityObjectRow->getCount();
        }
        if($countEnd > $limit){
            $this->error(self::TRANSPORT_NOT_EATING, $countEnd);
            return false;
        }
        $this->getFilter()->getTransportManager()->setFoodCost($countEnd);
        return true;
    }
    
    public function getEating($objectRow, $cityRow)
    {
        $attrRow = $objectRow->getObjectRow()->getAttrRowset()->getBy('attr_code', 'unit_eating');
        if ($attrRow) {
            $unit_eating = $cityRow->getUnitAttr($objectRow->getObjectRow(), $attrRow);
            return ceil($unit_eating * $objectRow->count / 100);
        }
        return 0;
    }
}