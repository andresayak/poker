<?php

namespace Game\Model\Lib\Starsystem;

trait Provider {

    use \Game\Provider\ProvidesServiceManager;
    
    protected $_starsystem_row;

    public function getStarsystemRow() 
    {
        if($this->_starsystem_row === null){
            $this->_starsystem_row = $this->getSm()->get('Lib\Starsystem\Table')->fetchBy('id', $this->starsystem_id);
        }
        return $this->_starsystem_row;
    }

    public function setStarsystemRow(Row $starsystemRow) 
    {
        $this->_starsystem_row = $starsystemRow;
        $this->starsystem_id = $starsystemRow->id;
        return $this;
    }
}
