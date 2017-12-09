<?php

namespace Game\Model\Lib\Npc;

use Ap\Model\Table as Prototype;

class Table extends Prototype
{
    protected $_name = 'lib_npc';
    protected $_key =  'code';
    protected $_cols = array(
        'code', 'title', 'type', 'filename'
    );
    
    public $types = array(
        'normal'=>    'normal'
    );
}