<?php

namespace Game\Model\Lib\Resource\Inside;

use Ap\Model\Table as Prototype;

class Table extends Prototype
{
    protected $_name = 'lib_resource_inside';
    protected $_key =  'id';
    protected $_cols = array(
        'id', 'depend_code', 'resource_code', 'count', 'probability', 'diff'
    );
    protected $_defaults = array(
        'probability'=>1.00,
        'diff' =>  0.00
    );
}