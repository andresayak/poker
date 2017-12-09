<?php

namespace Game\Model\Lib\Checkin;

trait Provider {

    use \Game\Provider\ProvidesServiceManager;
    
    protected $_checkin_row;

    public function getCheckinRow() 
    {
        if($this->_checkin_row === null){
            $this->_checkin_row = $this->getSm()->get('Lib\Checkin\Table')->fetchBy('id', $this->checkin_id);
        }
        return $this->_checkin_row;
    }

    public function setCheckinRow(Row $checkinRow) 
    {
        $this->_checkin_row = $checkinRow;
        return $this;
    }
}
