<?php

namespace Game\Model\Lib\Unit\Depend;

use Ap\Model\Table as Prototype;

class Table extends Prototype
{
    protected $_name = 'lib_unit_depend';
    
    protected $_cols = array(
        'id', 'unit_code', 'depend_code', 'level', 'type'
    );
}