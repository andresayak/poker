<?php

namespace Game\Model\Lib\Planet\Resource;

use Ap\Model\Table as Prototype;

class Table extends Prototype
{
    protected $_name = 'lib_planet_resource';
    
    protected $_cols = array(
        'id', 'planet_id', 'resource_code', 'count'
    );
}