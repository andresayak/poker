<?php

namespace Game\Validator;

use Game\Model\City\Row AS CityRow;

class EventArledyCreate extends AbstractValidator 
{
    protected $options = array(
        'type'          =>  null, 
        'object_id'     =>  false,
        'in_all_city'   =>  false
    );
    
    public function setType($type)
    {
        $this->options['type'] = $type;
        return $this;
    }
    
    public function setInAllCity($status)
    {
        $this->options['in_all_city'] = (bool)$status;
        return $this;
    }
    
    public function setObjectId($status)
    {
        $this->options['object_id'] = (bool)$status;
        return $this;
    }
    
    public function isValid($value)
    {
        $cityRow = $this->getFilter()->getCityRow();
        if($this->options['in_all_city']){
            $cityRowset = $this->getFilter()->getUserRow()->getCityRowset();
            foreach($cityRowset->getItems() AS $cityRow){
                if(!$this->_isValidCityRow($cityRow)){
                    return false;
                }
            }
        }else{
            $cityRow = $this->getFilter()->getCityRow();
            return $this->_isValidCityRow($cityRow);
        }
        
        return true;
    }
    
    protected function _isValidCityRow(CityRow $cityRow)
    {
        if($this->options['object_id']){
            $cityObjectRow = $this->getFilter()->getCityObjectRow();
            if($cityRow->getEventRowset()->getBy('object_id', $cityObjectRow->id)){
                $this->error(self::EVENT_ARLEDY_CREATED, $cityObjectRow->id);
                return false;
            }
        }elseif($this->options['type'] !== null){
            if($cityRow->getEventRowset()->getBy('type', $this->options['type'])){
                $this->error(self::EVENT_ARLEDY_CREATED, $this->options['type']);
                return false;
            }
        }else{
            $objectRow = $this->getFilter()->getObjectRow();
            if($cityRow->getEventRowset()->getBy('object_code', $objectRow->code)){
                $this->error(self::EVENT_ARLEDY_CREATED, $objectRow->code);
                return false;
            }
        }
        return true;
    }
}