<?php

namespace Game\Model\Lib\Npc;

trait Provider {

    use \Game\Provider\ProvidesServiceManager;
    
    protected $_npc_row;

    public function getNpcRow() 
    {
        if($this->_npc_row === null){
            $this->_npc_row = $this->getSm()->get('Lib\Npc\Table')->fetchBy('code', $this->npc_code);
        }
        return $this->_npc_row;
    }
    
    public function setNpcRow(Row $npcRow) 
    {
        $this->_npc_row = $npcRow;
        $this->npc_code = $npcRow->code;
        return $this;
    }

    public function setLevelRow(Row $levelRow) 
    {
        $this->_npc_row = $levelRow;
        return $this;
    }
}
