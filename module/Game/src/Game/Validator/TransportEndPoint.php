<?php

namespace Game\Validator;

class TransportEndPoint extends AbstractValidator 
{
    protected $options = array(
        'type'    => null, 
    );
    protected $types = array(
        'transport', 'attack', 'spy'
    );
    
    public function setType($type)
    {
        if(!in_array($type, $this->types)){
            throw new \Exception('Type invalid ['.$type.']');
        }
        $this->options['type'] = $type;
        return $this;
    }
    
    public function isValid($value)
    {
        $context = func_get_arg(1);
        
        if(!isset($context['x']) or !isset($context['y'])){
            throw new \Exception('Not found geo');
        }
        if($this->options['type'] === null){
            throw new \Exception('Type not set');
        }
        $x = $context['x'];
        $y = $context['y'];
        
        $cityRow = $this->getFilter()->getCityRow();
        $userRow = $this->getFilter()->getUserRow();
        $regionRow = $this->getFilter()->getRegionRow();
        
        $cityTable = $this->getFilter()->getSm()->get('City\Table');
        $regionVillageTable = $this->getFilter()->getSm()->get('Region\Village\Table');
        $regionNpcTable = $this->getFilter()->getSm()->get('Region\Npc\Table');
        
        if(($cityRow->geo_x == $x and $cityRow->geo_y == $y) 
            or ($regionRow->size < $x or $regionRow->size < $y)
        ){
            $this->error(self::TRANSPORT_GEO_INVALID);
            return false;
        }
            if($regionRow->capital_geo_x == $x and $regionRow->capital_geo_y == $y){
                switch ($this->options['type']) {
                    case 'attack':
                        if(!$this->attackToCapital($userRow)){
                            return false;
                        }
                        break;
                    case 'transport':
                        if(!$this->transportToCapital($userRow)){
                            return false;
                        }
                        break;
                    default: 
                        $this->error(self::TRANSPORT_GEO_INVALID);
                        return false;
                }
            }elseif($cityDefRow = $cityTable->fetchByRegionIdAndXY($regionRow->id, $x, $y)){
                switch ($this->options['type']) {
                    case 'attack':
                        if(!$this->attackToCity($cityRow, $cityDefRow)){
                            return false;
                        }
                        break;
                    case 'spy':
                        if($cityRow->user_id == $cityDefRow->user_id){
                            $this->error(self::SPY_YOUR_CITY);
                            return false;
                        }
                        break;
                    case 'transport':
                        if(($cityRow->user_id != $cityDefRow->user_id) 
                            and !$cityRow->getUserRow()->isFriend($cityDefRow->user_id)
                            and (
                                !$cityRow->getUserRow()->getMemberRow() 
                                or !$cityDefRow->getUserRow()->getMemberRow()
                                or !$cityRow->getUserRow()->getMemberRow()->alliance_id == $cityDefRow->getUserRow()->getMemberRow()->alliance_id
                            )
                        ){
                            $this->error(self::TRANSPORT_NO_YOUR_CITY);
                            return false;
                        }
                        break;
                    default: 
                        $this->error(self::TRANSPORT_GEO_INVALID);
                        return false;
                }
            }elseif($villageRow = $regionVillageTable->fetchByRegionIdAndXY($regionRow->id, $x, $y)){
                switch ($this->options['type']) {
                    case 'attack':
                        if(!(!$villageRow->city_id or !$userRow->getCityRowset()->getBy('id', $villageRow->city_id))){
                            $this->error(self::ATTACK_YOUR_VILLAGE);
                            return false;
                        }
                        break;
                    case 'spy':
                        if(!(!$villageRow->city_id or !$userRow->getCityRowset()->getBy('id', $villageRow->city_id))){
                            $this->error(self::ATTACK_YOUR_VILLAGE);
                            return false;
                        }
                        break;
                    case 'transport':
                        if(!($villageRow->city_id and $userRow->id == $villageRow->getCityRow()->user_id)){
                            $this->error(self::TRANSPORT_NO_YOUR_VILLAGE);
                            return false;
                        }
                        break;
                    default: 
                        $this->error(self::TRANSPORT_GEO_INVALID);
                        return false;
                }
            }elseif($npcRow = $regionNpcTable->fetchByRegionIdAndXY($regionRow->id, $x, $y)){
                if($this->options['type'] == 'transport'){
                    $this->error(self::TRANSPORT_GEO_INVALID);
                    return false;
                }
            }else{
                $this->error(self::TRANSPORT_GEO_INVALID);
                return false;
            }
        return true;
    }
    
    protected function attackToCapital($userRow)
    {
        $regionRow = $this->getFilter()->getRegionRow();
        
        if (!$userRow->getMemberRow()) {
            $this->error(self::ATTACK_CAPITAL_NO_ALLIANCE);
            return false;
        }
        if ($regionRow->getAllianceRow() 
            and $userRow->getMemberRow() 
            and $regionRow->getAllianceRow()->id == $userRow->getMemberRow()->alliance_id
        ) {
            $this->error(self::ATTACK_YOUR_CAPITAL);
            return false;
        }
        $regionBattleTable = $this->getFilter()->getSm()->get('Region\Battle\Table');
        $battleRow = $regionBattleTable->fetchActiveByAllianceIdAndRegionId(
            $userRow->getMemberRow()->alliance_id, $regionRow->id);
        
        if (!$battleRow) {
            $this->error(self::REGION_WAR_NOT_CREATE);
            return false;
        }
        if (!$battleRow->isStart()) {
            $this->error(self::REGION_WAR_NOT_START);
            return false;
        }
        $time_end = $this->getFilter()->getTransportManager()->getTransportRow()->time_start + $this->getFilter()->getTransportManager()->calculateTime();
        if ($time_end > $battleRow->time_end) {
            $this->error(self::DEFEND_CAPITAL_NO_TIME);
            return false;
        }
        $this->getFilter()->setBattleRow($battleRow);
               
        return true;
    }

    protected function attackToCity($cityRow, $cityDefRow)
    {
        if ($cityRow->user_id == $cityDefRow->user_id) {
            $this->error(self::ATTACK_YOUR_CITY);
            return true;
        } else {
            if($cityDefRow->isCap()){
                $this->error(self::ATTACK_CITY_IN_CAP);
                return false;
            }
        }
        return true;
    }
    
    protected function transportToCapital($userRow) 
    {
        $regionRow = $this->getFilter()->getRegionRow();
        
        $regionBattleTable = $this->getFilter()->getSm()->get('Region\Battle\Table');
        $battleRow = $regionBattleTable->fetchNotEnd($regionRow->id);
        
        if (!$battleRow) {
            $this->error(self::REGION_WAR_NOT_CREATE);
            return false;
        }
        if (!$battleRow->isStart()) {
            $this->error(self::REGION_WAR_NOT_START);
            return false;
        }
        
        if (!$regionRow->getAllianceRow() 
            or !$userRow->getMemberRow() 
            or $regionRow->getAllianceRow()->id != $userRow->getMemberRow()->alliance_id
        ) {
            $this->error(self::DEFEND_NO_YOUR_CAPITAL);
            return false;
        }
        return true;
    }

}