<?php

namespace Game\Model\Lib\Npc\Guard;

use Ap\Model\Table as Prototype;

class Table extends Prototype
{
    protected $_name = 'lib_npc_guard';
    
    protected $_cols = array(
        'id', 'npc_code', 'unit_code', 'probability'
    );
}