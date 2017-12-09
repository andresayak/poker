<?php

namespace Game\Model\Lib\Citysize\Need;

use Ap\Model\Table as Prototype;

class Table extends Prototype
{
    protected $_name = 'lib_citysize_need';
    
    protected $_cols = array(
        'id', 'citysize_id', 'resource_code', 'count'
    );
}