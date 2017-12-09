<?php

namespace Game\Model\User\Attr;

use Ap\Model\Table as Prototype;

class Table extends Prototype
{
    protected $_name = 'user_attr';
    protected $_cols = array(
        'id', 'user_id', 
        'attr_code', 'value'
    );
    
    public function fetchAllByUserId($user_id)
    {
        $this->getTableGateway()->initialize();
        return $this->getTableGateway()->select(array('user_id'=>$user_id));
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