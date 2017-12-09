<?php

namespace Game\Validator;

class CheckWallLevel extends AbstractValidator 
{
    protected $options = array(
        'callback'  =>  null,
        'rowset'    =>  null,
        'key'       =>  'code',
        'rand'      =>  false
    );
    
    public function isValid($value)
    {
        
        $nextLevel = $this->getFilter()->getCityObjectRow()->level+1;
        if($nextLevel > $this->getFilter()->getCityRow()->getAttrValue('wall_cell_maxlevel')){
            $this->error(self::WALL_MAX_LEVEL_LIMIT);
            return false;
        }
        $level_row = ($this->getFilter()->getCityObjectRow()->getObjectRow()->getLevelRowset()->getBy('level', $nextLevel));
        if(!(bool) $level_row){
            $this->error(self::WALL_MAX_LEVEL);
            return false;
        }
        $this->getFilter()->setLevelRow($level_row);
        return true;
    }
}