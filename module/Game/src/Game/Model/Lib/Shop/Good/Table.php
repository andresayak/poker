<?php

namespace Game\Model\Lib\Shop\Good;

use Ap\Model\Table as Prototype;

class Table extends Prototype
{
    protected $_name = 'lib_shop_good';
    
    protected $_cols = array(
        'id', 'shop_code', 'resource_code', 'count'
    );
}