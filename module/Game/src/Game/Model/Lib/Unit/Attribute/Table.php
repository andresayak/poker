<?php

namespace Game\Model\Lib\Unit\Attribute;

use Ap\Model\Table as Prototype;

class Table extends Prototype
{
    protected $_name = 'lib_unit_attribute';
    
    protected $_cols = array(
        'id', 'unit_code', 'attribute_code', 'value_int', 'value_bool', 'value_select', 
        'value_float'
    );
}