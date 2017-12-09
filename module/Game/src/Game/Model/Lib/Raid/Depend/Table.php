<?php

namespace Game\Model\Lib\Raid\Depend;

use Ap\Model\Table as Prototype;

class Table extends Prototype
{
    protected $_name = 'lib_raid_depend';
    
    protected $_cols = array(
        'id', 'raid_code', 'depend_code'
    );
}