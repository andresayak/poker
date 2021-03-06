<?php

namespace Game\Model\Lib\Saga;

use Ap\Model\Table as Prototype;

class Table extends Prototype
{
    protected $_name = 'lib_saga';
    protected $_key =  'code';
    protected $_cols = array(
        'code', 'title', 'filename', 'level'
    );
}