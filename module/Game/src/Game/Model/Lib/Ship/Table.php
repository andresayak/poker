<?php

namespace Game\Model\Lib\Ship;

use Ap\Model\Table as Prototype;

class Table extends Prototype
{
    protected $_name = 'lib_ship';
    protected $_key =  'code';
    protected $_cols = array(
        'code', 'title', 'filename', 'type', 'building_code'
    );
    
    public $types = array(
        'a1' => 'A1',
        'a2' => 'A2',
        'a3' => 'A3',
        'a4' => 'A4',
        'a5' => 'A5',
        'a6' => 'A6',
        'a7' => 'A7',
        'd' => 'D',
        'tp' => 'TP',
        't' => 'T',
        'op' => 'OP',
    );
}