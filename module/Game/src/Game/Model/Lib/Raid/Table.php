<?php

namespace Game\Model\Lib\Raid;

use Ap\Model\Table as Prototype;

class Table extends Prototype
{
    protected $_name = 'lib_raid';
    protected $_key =  'code';
    protected $_cols = array(
        'code', 'title', 'filename', 'level'
    );
}