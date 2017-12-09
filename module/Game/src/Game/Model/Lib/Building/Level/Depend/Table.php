<?php

namespace Game\Model\Lib\Building\Level\Depend;

use Ap\Model\Table as Prototype;

class Table extends Prototype
{
    protected $_name = 'lib_building_level_depend';
    
    protected $_cols = array(
        'id', 'level_id', 'depend_code', 'level', 'type'
    );
}