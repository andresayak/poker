<?php

namespace Game\Model\Lib\Skill\Level\Attribute;

use Ap\Model\Table as Prototype;

class Table extends Prototype
{
    protected $_name = 'lib_skill_level_attribute';
    
    protected $_cols = array(
        'id', 'level_id', 'attribute_code', 'value_int', 'value_bool', 'value_select', 
        'value_float'
    );
}