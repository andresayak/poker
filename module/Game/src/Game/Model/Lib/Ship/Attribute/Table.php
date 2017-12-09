<?php

namespace Game\Model\Lib\Ship\Attribute;

use Ap\Model\Table as Prototype;

class Table extends Prototype
{
    protected $_name = 'lib_ship_attribute';
    
    protected $_cols = array(
        'id', 'ship_code', 'attribute_code', 'value_int', 'value_bool', 'value_select', 
        'value_float'
    );
}