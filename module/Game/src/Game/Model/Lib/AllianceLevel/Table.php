<?php

namespace Game\Model\Lib\AllianceLevel;

use Ap\Model\Table as Prototype;

class Table extends Prototype
{
    protected $_name = 'lib_allianceLevel';
    
    protected $_cols = array(
        'id', 'title', 'member_count', 'score'
    );
}