<?php

namespace Game\Model\User\Object;

use Ap\Model\Table as Prototype;

class Table extends Prototype
{
    protected $_name = 'user_object';
    protected $_cols = array(
        'id', 'user_id', 'object_code', 
        'count'
    );
}