<?php

namespace Game\Model\Lib\Ship\Weapon;

use Ap\Model\Table as Prototype;

class Table extends Prototype
{
    protected $_name = 'lib_ship_weapon';
    
    protected $_cols = array(
        'id', 'ship_code', 'slot_id', 'resource_code'
    );
}