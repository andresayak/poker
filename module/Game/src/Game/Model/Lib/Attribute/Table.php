<?php

namespace Game\Model\Lib\Attribute;

use Ap\Model\Table as Prototype;

class Table extends Prototype
{
    protected $_name = 'lib_attribute';
    protected $_key =  'code';
    protected $_cols = array(
        'code', 'title', 'filename', 'datatype', 'assignation', 'typechange',
        'max_value', 'min_value', 'default_value', 'default_id'
    );
    
    public $datatypes = array(
        'int'=>'Number', 'bool'=>'Yes/No', 'float'=>'Percent', 'select'=>'List'
    );
    public $assignations = array(
        'unit'=>'for Unit', 
        'ship'=>'for Ship', 
        'city'=>'for City', 
        'user'=>'for User', 
        'transport'=>'for Transport'
    );
    public $typechanges = array(
        'auto'=>'Auto', 'manual'=>'Manual'
    );
}