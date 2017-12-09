<?php

namespace Game\Model\Lib\Unit;

use Ap\Model\Table as Prototype;

class Table extends Prototype
{
    protected $_name = 'lib_unit';
    protected $_key =  'code';
    protected $_cols = array(
        'code', 'title', 'filename', 'type', 'building_code'
    );
    public $types = array(
        'air'=>'Air'
    );
}