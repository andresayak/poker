<?php

namespace Game\Model\Lib\Starsystem\Gate;

use Ap\Model\Table as Prototype;

class Table extends Prototype
{
    protected $_name = 'lib_starsystem_gate';
    
    protected $_cols = array(
        'id', 'starsystem_id', 'depend_id', 'angle'
    );
}