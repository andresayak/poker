<?php

namespace Game\Model\Lib\Attribute\Option;

use Ap\Model\Table as Prototype;

class Table extends Prototype
{
    protected $_name = 'lib_attribute_option';
    protected $_key =  'id';
    protected $_cols = array(
        'id', 'attribute_code', 'title', 'value'
    );
}