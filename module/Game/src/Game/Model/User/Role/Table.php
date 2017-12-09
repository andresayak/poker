<?php

namespace Game\Model\User\Role;

use Ap\Model\Table as Prototype;

class Table extends Prototype
{
    protected $_name = 'user_role';
    protected $_cols = array(
        'code', 'title', 'parent', 'priority'
    );
    
    public function fetchAll()
    {
        return $this->cached(function(){
            $select = $this->getTableGateway()->getSql()->select()
                ->order('priority ASC');
            return $this->getTableGateway()->selectWith($select);
        }, '->fetchAll()');
    }
}