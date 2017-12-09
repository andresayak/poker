<?php

namespace Game\Model\User\Uid;

use Ap\Model\Table as Prototype;

class Table extends Prototype
{
    protected $_name = 'user_uid';
    protected $_cols = array(
        'id', 'user_id', 'uid', 'time_connect', 'token', 'auth_type'
    );
    
    public function fetchByUid($uid)
    {
        $this->getTableGateway()->initialize();
        return $this->getTableGateway()->select(array('uid'=>$uid))->current();
    }
    
    public function fetchAllActive($user_id)
    {
        $this->getTableGateway()->initialize();
        return $this->getTableGateway()->select(array(
            'user_id'           =>  $user_id,
        ));
    }

    public function add(Row $row)
    {
        $row->save();
        return $this;
    }
    
    public function edit(Row $row)
    {
        $row->save();
        return $this;
    }
    
    public function delete(Row $row)
    {
        $row->delete();
        return $this;
    }
}