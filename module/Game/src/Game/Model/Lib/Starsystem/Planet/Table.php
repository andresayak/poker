<?php

namespace Game\Model\Lib\Starsystem\Planet;

use Ap\Model\Table as Prototype;

class Table extends Prototype
{
    protected $_name = 'lib_starsystem_planet';
    
    protected $_cols = array(
        'id', 'title', 'starsystem_id', 'size', 'speed', 'distance'
    );
}