<?php

namespace Game\Model\Lib\Shop;

use Ap\Model\Table as Prototype;

class Table extends Prototype
{
    protected $_name = 'lib_shop';
    protected $_key =  'code';
    protected $_cols = array(
        'code', 'filename', 'title'
    );
}