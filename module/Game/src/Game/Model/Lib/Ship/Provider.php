<?php

namespace Game\Model\Lib\Ship;

trait Provider {

    use \Game\Provider\ProvidesServiceManager;
    
    protected $_ship_row;

    public function getShipRow() 
    {
        if($this->_ship_row === null){
            $this->_ship_row = $this->getSm()->get('Lib\Ship\Table')->fetchBy('code', $this->ship_code);
        }
        return $this->_ship_row;
    }

    public function setShipRow(Row $shipRow) 
    {
        $this->_ship_row = $shipRow;
        $this->ship_code = $shipRow->code;
        return $this;
    }
}
