<?php

namespace Game\Model\Lib\Starsystem\Planet;

trait Provider {

    use Game\Provider\ProvidesServiceManager;
    
    protected $_planet_row;

    public function getPlanetRow() 
    {
        if($this->_planet_row === null){
            $this->_planet_row = $this->getSm()->get('Lib\Starsystem\Planet\Table')->fetchBy('id', $this->planet_id);
        }
        return $this->_attribute_row;
    }

    public function setPlanetRow(Row $planetRow) 
    {
        $this->_planet_row = $planetRow;
        return $this;
    }
}
