<?php

namespace Game\Model\Lib\Resource;

use Ap\Model\Table as Prototype;

class Table extends Prototype
{
    protected $_name = 'lib_resource';
    protected $_key =  'code';
    protected $_cols = array(
        'code', 'title', 'filename', 'type', 'default_count', 'size'
    );
    protected $_defaults = array(
        'default_count' =>  null
    );
    
    public $sizes = array(
        's' => 'S',
        'm' => 'M',
        'l' => 'L',
        'xl' => 'XL'
    );
    
    public $types = array(
        'material'      =>  'Material',
        'shild'         =>  'Shild',
        'weapon'        =>  'Weapon',
        'equip'         =>  'Equip',
        'product'       =>  'Product',
        'fragment'      =>  'Fragment',
        'scheme'        =>  'Scheme',
        'box'           =>  'Box',
        'misc'          =>  'Misc'
    );
}