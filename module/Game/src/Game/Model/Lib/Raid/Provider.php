<?php

namespace Game\Model\Lib\Raid;

trait Provider {

    use \Game\Provider\ProvidesServiceManager;
    
    protected $_raid_row;

    public function getRaidRow() 
    {
        if($this->_raid_row === null){
            $this->_raid_row = $this->getSm()->get('Lib\Raid\Table')->fetchBy('code', $this->raid_code);
        }
        return $this->_raid_row;
    }

    public function setSagaRow(Row $sagaRow) 
    {
        $this->_raid_row = $sagaRow;
        return $this;
    }
}
