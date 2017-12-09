<?php

namespace Game\Model\Lib\Skill;

trait Provider {

    use \Game\Provider\ProvidesServiceManager;
    
    protected $_skill_row;

    public function getSkillRow() 
    {
        if($this->_skill_row === null){
            $this->_skill_row = $this->getSm()->get('Lib\Skill\Table')->getRowset()->getBy('code', $this->skill_code);
        }
        return $this->_skill_row;
    }

    public function setSkillRow(Row $skillRow) 
    {
        $this->_skill_row = $skillRow;
        $this->skill_code = $skillRow->code;
        return $this;
    }
}
