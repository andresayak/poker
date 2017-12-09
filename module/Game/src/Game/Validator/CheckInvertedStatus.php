<?php

namespace Game\Validator;

class CheckInvertedStatus extends AbstractValidator 
{
    protected $options = array(
    );
    
    public function isValid($value)
    {
        $cityRow = $this->getFilter()->getCityRow();
        if(!$cityRow->getAttrValue('inverted_status')){
            $this->error(self::INVERTED_IS_DISABLED, 'Inverted is disabled');
            return false;
        }
        return true;
    }
}