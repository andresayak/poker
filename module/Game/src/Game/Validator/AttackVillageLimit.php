<?php

namespace Game\Validator;

class AttackVillageLimit extends AbstractValidator 
{
    protected $options = array(
        'type'    => null, 
    );
    
    
    public function setType($type)
    {
        $this->options['type'] = $type;
        return $this;
    }
    
    public function isValid($value)
    {
        if($this->options['type'] != 'attack'){
            return true;
        }
        $cityRow = $this->getFilter()->getCityRow();
        $villageRow = $this->getFilter()->getTransportManager()->getTransportRow()->getVillageToRow();
        if($villageRow){
            if($villageRow->object_code == 'gunpowder'){
                $limit = $cityRow->getAttrValue('gunpowder_count_limit');
                $count = $cityRow->getVillageRowset()->getCountIsActiveAndIfGunpowder(true);
                if($count >= $limit){
                    $this->error(self::ATTACK_GUNPOWDER_LIMIT);
                    return false;
                }
            }else{
                $limit = $cityRow->getAttrValue('village_limit');
                $count = $cityRow->getVillageRowset()->getCountIsActiveAndIfGunpowder(false);
                if($count >= $limit){
                    $this->error(self::ATTACK_VILLAGE_LIMIT);
                    return false;
                }
            }
        }
        return true;
    }
}