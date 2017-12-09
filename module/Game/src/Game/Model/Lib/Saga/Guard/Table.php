<?php

namespace Game\Model\Lib\Saga\Guard;

use Ap\Model\Table as Prototype;

class Table extends Prototype
{
    protected $_name = 'lib_saga_guard';
    
    protected $_cols = array(
        'id', 'saga_code', 'resource_code', 'probability'
    );
}