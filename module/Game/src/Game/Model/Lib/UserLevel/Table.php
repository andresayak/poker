<?php

namespace Game\Model\Lib\UserLevel;

use Ap\Model\Table as Prototype;

class Table extends Prototype
{
    protected $_name = 'lib_leveluser';
    protected $_key = 'level';
    
    protected $_cols = array(
        'level', 'exp'
    );
}