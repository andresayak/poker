<?php

namespace Game\Model\Lib\Unit\Need;

use Ap\Model\Table as Prototype;

class Table extends Prototype
{
    protected $_name = 'lib_unit_need';
    
    protected $_cols = array(
        'id', 'unit_code', 'resource_code', 'count', 'probability', 'diff'
    );
}