<?php

namespace Game\Validator;

class CheckTreatCost extends AbstractValidator 
{
    protected $options = array(
    );
    
    public function isValid($value)
    {
        $cityRow = $this->getFilter()->getCityRow();
        $cost = $cityRow->getAttrValue('treat_cost');
        $sumCost = 0;
        foreach($this->getFilter()->getList() AS $count) {
            $sumCost+= $cost * $count;
        }
        $cityObjectRow = $cityRow->getObjectRowset()->getBy('object_code', 'gold');
        $cityObjectRow->blockForUpdate();
        if($cityObjectRow->getCount() < $sumCost){
            $this->error(self::OBJECT_NOT_ENOUGH_COUNT, 'gold');
            return false;
        }
        $this->getFilter()->setCost($sumCost);
        return true;
    }
}