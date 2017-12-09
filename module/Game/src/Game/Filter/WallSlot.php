<?php

namespace Game\Filter;

class WallSlot extends AbstractFilter
{
    public function filter($value)
    {
        if($value){
           return $value; 
        }
        for($i = 1; $i <= 10; $i++){
            $status = true;
            foreach ($this->getFilter()->getCityRow()->getObjectRowset()->getItems() AS $objectRow) {
                if ($objectRow->getObjectRow()->unit_recruit == 'wall' and $objectRow->slot == $i) {
                    $status = false;
                }
            }
            if($status){
                return $i;
            }
        }
        
        return 1;
    }
}