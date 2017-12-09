<?php

namespace Game\Model\Lib\Saga\Depend;

use Ap\Model\Table as Prototype;

class Table extends Prototype
{
    protected $_name = 'lib_saga_depend';
    
    protected $_cols = array(
        'id', 'saga_code', 'depend_code'
    );
}