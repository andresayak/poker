<?php

namespace Game\Model\Lib\Object;

use Ap\Model\Table as Prototype;

class Table extends Prototype
{
    protected $_name = 'lib_object';
    protected $_key =  'code';
    protected $_cols = array(
        'code', 'default_count'
    );
    
}