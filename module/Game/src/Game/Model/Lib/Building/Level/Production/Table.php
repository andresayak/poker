<?php

namespace Game\Model\Lib\Building\Level\Production;

use Ap\Model\Table as Prototype;

class Table extends Prototype
{
    
    protected $_name = 'lib_building_level_production';
    
    protected $_cols = array(
        'id', 'level_id', 'resource_code', 'count', 'period'
    );
}