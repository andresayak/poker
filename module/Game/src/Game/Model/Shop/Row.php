<?php

namespace Game\Model\Shop;

use Ap\Model\Row as Prototype;

class Row extends Prototype
{
    protected $_object_rowset, $_event_row;
    public $time_start, $time_end;
    
    public function promoTime($count, $offset)
    {
        if(!$this->promo_period){
            throw new \Exception('promo_period not set for id='.$this->id);
        }
        $timeNow = time();
        $period = $this->promo_period;
        $current = floor($timeNow / $period);
        if($count){
            $index = $current % $count;
            $diff = ($count - $index + $offset)% $count;
            $this->time_start = ($current + $diff) * $period;
            $this->time_end = ($this->time_start + $period)-1;
        } else {
            $this->time_start = 0;
            $this->time_end = 0;
        }
    }
    
    public function getEventRow()
    {
        if($this->_event_row === null){
            $this->_event_row = ($this->event_id)?$this->getSm()->get('Event\Table')->fetchBy('id', $this->event_id):false;
        }
        return $this->_event_row;
    }
    
    public function getObjectRowset()
    {
        if($this->_object_rowset === null){
            $this->_object_rowset = $this->getSm()->get('Shop\Object\Table')->getRowset()->getRowsetBy('shop_id', $this->id);
        }
        return $this->_object_rowset;
    }

    public function isPromoActive($timeNow = null)
    {
        $timeNow = ($timeNow === null)?time():$timeNow;
        return ($this->time_start <= $timeNow and $timeNow < $this->time_end);
    }
    
    public function toArrayForApi($recursive = true, $cacheData = false)
    {
        $data = $this->toArray();
        if($this->isPromo()){
            $data['promo_time_start'] = $this->time_start;
            $data['promo_time_end'] = $this->time_end;
            $data['promo_active'] = $this->isPromoActive();
        }
        $data['object_rowset'] = $this->getObjectRowset()->toArrayForApi();
        return $data;
    }
    
    public function isBuyGem()
    {
        return (in_array($this->type, array('gem', 'promo_gem')));
    }
    
    public function isPromo()
    {
        return (in_array($this->type, array('promo', 'promo_gem')));
    }
    
    public function buyEvent($userRow)
    {
        $resultTable = $this->getSm()->get('Event\Result\Table');
        $resultRow = $resultTable->createRow(array(
            'event_id' => $this->getEventRow()->id,
            'user_id' => $userRow->id,
            'time_add' => time(),
            'value' => 0,
            'position' => null
        ));
        $resultRow->save();
    }
    
    public function buy($userRow, $count)
    {
        foreach($this->getObjectRowset()->getItems() AS $objectRow){
            $countItem = $objectRow->count * $count;
            $userRow->updateObjectRow($objectRow->object_code, $countItem);
        }
    }
}