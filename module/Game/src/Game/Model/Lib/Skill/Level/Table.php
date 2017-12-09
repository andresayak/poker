<?php

namespace Game\Model\Lib\Skill\Level;

use Ap\Model\Table as Prototype;

class Table extends Prototype
{
    protected $_name = 'lib_skill_level';
    
    protected $_cols = array(
        'id', 'skill_code', 'level', 'time_build', 'exp', 'user_level_need'
    );
    
    protected $_defaults = array(
        'time_build'    =>  0,
        'exp'           =>  0,
        'user_level_need'   =>  null
    );
}