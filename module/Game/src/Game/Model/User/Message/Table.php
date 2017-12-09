<?php

namespace Game\Model\User\Message;

use Ap\Model\Table as Prototype;
use \Zend\Db\Sql\Expression;

class Table extends Prototype
{
    protected $_name = 'user_message';
    protected $_cols = array(
        'id', 'type', 'favorite_from','favorite_to', 'subject', 'message',
        'user_id_to', 'user_id_from', 'time_create', 'time_read', 'remove_from', 'remove_to'
    );
    protected $_defaults = array(
        'favorite_from'   =>  0,
        'favorite_to'   =>  0,
        'remove_from'   =>  0,
        'remove_to'   =>  0,
        'type'  =>  'private',
        'time_read' =>  null
    );
    
}