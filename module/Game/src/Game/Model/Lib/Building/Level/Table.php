<?php

namespace Game\Model\Lib\Building\Level;

use Ap\Model\Table as Prototype;

class Table extends Prototype
{
    protected $_name = 'lib_building_level';
    
    protected $_cols = array(
        'id', 'building_code', 'level', 'time_build', 'exp', 'user_level_need'
    );
    
    protected $_defaults = array(
        'time_build'    =>  0,
        'exp'           =>  0,
        'user_level_need'   =>  null
    );
}