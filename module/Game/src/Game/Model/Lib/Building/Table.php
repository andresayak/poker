<?php

namespace Game\Model\Lib\Building;

use Ap\Model\Table as Prototype;

class Table extends Prototype
{
    protected $_name = 'lib_building';
    protected $_key =  'code';
    protected $_cols = array(
        'code', 'title', 'filename', 'type', 'limit'
    );
    
    public $types = array(
        'resource'  =>  'Resource',
        'factory'   =>  'Factory',
        'military'  =>  'Military',
    );
}