<?php

namespace Game\Model\Lib\Skill\Level;

trait Provider {

    use \Game\Provider\ProvidesServiceManager;
    
    protected $_level_row;

    public function getLevelRow() 
    {
        if($this->_level_row === null){
            $this->_level_row = $this->getSm()->get('Lib\Skill\Level\Table')->fetchBy('id', $this->level_id);
        }
        return $this->_level_row;
    }

    public function setLevelRow(Row $levelRow) 
    {
        $this->_level_row = $levelRow;
        $this->level_id = $levelRow->id;
        return $this;
    }
}
