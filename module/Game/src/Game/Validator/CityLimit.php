<?php

namespace Game\Validator;

class CityLimit extends AbstractValidator 
{
    public function isValid($value)
    {
        $count = count($this->getFilter()->getUserRow()->getCityRowset());
        $limit = $this->getFilter()->getUserRow()->getAttrValue('city_limit');
        if($count >= $limit){
            $this->error(self::CITYLIMIT, $limit);
            return false;
        }
        return true;
    }
}