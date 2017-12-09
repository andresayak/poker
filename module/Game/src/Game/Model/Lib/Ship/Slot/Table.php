<?php

namespace Game\Model\Lib\Ship\Slot;

use Ap\Model\Table as Prototype;

class Table extends Prototype
{
    protected $_name = 'lib_ship_slot';
    
    protected $_cols = array(
        'id', 'name', 'ship_code', 'x', 'y'
    );
}