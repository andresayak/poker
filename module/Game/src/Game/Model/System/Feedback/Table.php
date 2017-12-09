<?php

namespace Game\Model\System\Feedback;

use Ap\Model\Table as Prototype;

class Table extends Prototype
{
    protected $_key = 'id';
    protected $_name = 'system_feedback';
    protected $_cols = array(
        'id', 'name', 'email', 'message', 'replay_id', 
        'time_add', 'subject', 'ip_address', 'user_id'
    );
    
    public function add(Row $row)
    {
        $row->save();
    }
}