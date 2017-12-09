<?php

namespace Game\Model\Lib\Npc\Level\Loot;

use Ap\Model\Table as Prototype;

class Table extends Prototype
{
    protected $_name = 'lib_npc_level_loot';
    
    protected $_cols = array(
        'id', 'level_id', 'resource_code', 'count', 'probability', 'diff'
    );
}