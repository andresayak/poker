<?php

namespace Game\Model\Lib\Skill\Level\Depend;

use Ap\Model\Table as Prototype;

class Table extends Prototype
{
    protected $_name = 'lib_skill_level_depend';
    
    protected $_cols = array(
        'id', 'level_id', 'depend_code', 'level', 'type'
    );
}