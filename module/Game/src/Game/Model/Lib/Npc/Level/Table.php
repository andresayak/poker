<?php

namespace Game\Model\Lib\Npc\Level;

use Ap\Model\Table as Prototype;

class Table extends Prototype
{
    protected $_name = 'lib_npc_level';
    protected $_key =  'id';
    protected $_cols = array(
        'id', 'level', 'npc_code', 'def', 'prison_rate'
    );
}