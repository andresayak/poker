<?php

namespace Game\Model\Lib\Citysize\Depend;

use Ap\Model\Table as Prototype;

class Table extends Prototype
{
    protected $_name = 'lib_citysize_depend';
    
    protected $_cols = array(
        'id', 'citysize_id', 'depend_code'
    );
}