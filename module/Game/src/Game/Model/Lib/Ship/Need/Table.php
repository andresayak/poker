<?php

namespace Game\Model\Lib\Ship\Need;

use Ap\Model\Table as Prototype;

class Table extends Prototype
{
    protected $_name = 'lib_ship_need';
    
    protected $_cols = array(
        'id', 'ship_code', 'resource_code', 'count'
    );
}