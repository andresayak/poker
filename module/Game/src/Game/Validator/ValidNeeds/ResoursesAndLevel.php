<?php

namespace Game\Validator\ValidNeeds;

use Game\Validator\AbstractValidator;

class ResoursesAndLevel extends AbstractValidator 
{
    public function isValid($val)
    {
        $cityRow = $this->getFilter()->getCityRow();
        $table = $this->getFilter()->getSm()->get('City\Table');
        $table->updateResources($cityRow);// -- Deadlock found when trying to get lock; try restarting transaction
        foreach($this->getFilter()->getNeedRowSet() AS $object_code => $value){
            $objectRow = $this->getFilter()->getSm()->get('Lib\Object\Rowset')->getBy('code', $object_code);
            if(!$objectRow){
                $this->error(self::OBJECT_NO_FOUND, $object_code);
                return false;
            }
            if($objectRow->type == 'item'){
                $needRow = $cityRow->getUserRow()->getObjectRowset()->getBy('object_code', $object_code);
            }else{
                $needRow = $cityRow->getObjectByCode($object_code);
            }
            if(!$needRow){
                if($objectRow->isLevelType())
                    $this->error(self::OBJECT_NOT_ENOUGH_LEVEL, $object_code);
                elseif($objectRow->isCountType())
                    $this->error(self::OBJECT_NOT_ENOUGH_COUNT, $object_code);
                return false;
            }elseif($objectRow->isCountType()){
                if($value > $needRow->count){
                    $this->error(self::OBJECT_NOT_ENOUGH_COUNT, $object_code);
                    return false;
                }
            }elseif ($objectRow->isLevelType()) {
                if($value > $needRow->level){
                    $this->error(self::OBJECT_NOT_ENOUGH_LEVEL, $object_code);
                    return false;
                }
            }
        }
        return true;
    }
}