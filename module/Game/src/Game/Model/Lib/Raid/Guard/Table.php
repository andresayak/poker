<?php

namespace Game\Model\Lib\Raid\Guard;

use Ap\Model\Table as Prototype;

class Table extends Prototype
{
    protected $_name = 'lib_raid_guard';
    
    protected $_cols = array(
        'id', 'raid_code', 'resource_code', 'probability'
    );
}