<?php

namespace Game\Model\Lib\Building\Level\Production\Need;

use Ap\Model\Table as Prototype;

class Table extends Prototype
{
    protected $_name = 'lib_building_level_production_need';
    
    protected $_cols = array(
        'id', 'production_id', 'resource_code', 'count', 'level_id'
    );
}