<?php

namespace Game\Validator;

class CheckWallSlot extends AbstractValidator 
{
    public function isValid($value)
    {
        foreach ($this->getFilter()->getCityRow()->getObjectRowset()->getItems() AS $objectRow) {
            if ($objectRow->getObjectRow()->unit_recruit == 'wall' and $objectRow->slot == $value) {
                $this->error(self::WALL_SLOT);
                return false;
            }
        }
        return true;
    }
}