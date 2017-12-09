<?php

namespace Game\Model\Lib\Raid\Loot;

use Ap\Model\Table as Prototype;

class Table extends Prototype
{
    protected $_name = 'lib_raid_loot';
    
    protected $_cols = array(
        'id', 'raid_code', 'resource_code', 'count', 'probability', 'diff'
    );
}