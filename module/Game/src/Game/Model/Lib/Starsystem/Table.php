<?php

namespace Game\Model\Lib\Starsystem;

use Ap\Model\Table as Prototype;

class Table extends Prototype
{
    protected $_name = 'lib_starsystem';
    
    protected $_cols = array(
        'id', 'title', 'filename'
    );
}