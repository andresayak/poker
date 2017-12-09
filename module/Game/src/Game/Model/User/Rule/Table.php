<?php

namespace Application\Model\User\Rule;

use Ap\Model\Table as Prototype;

class Table extends Prototype
{
    protected $_name = 'user_rules';
    protected $_cols = array(
        'id', 'role', 'resource',
        'permission'
    );
}