<?php

namespace Game\Model\Lib\Skill;

use Ap\Model\Table as Prototype;

class Table extends Prototype
{
    protected $_name = 'lib_skill';
    protected $_key =  'code';
    protected $_cols = array(
        'code', 'filename', 'title', 'type', 'limit', 'building_code'
    );
    
    public $types = array(
        'resource'  =>  'Resource',
        'factory'   =>  'Factory',
        'military'  =>  'Military',
    );
}