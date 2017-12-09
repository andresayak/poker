<?php

namespace Game\Model\User\Interaction;

use Ap\Model\Table as Prototype;
use Zend\Db\Sql\Predicate;

class Table extends Prototype
{
    protected $_name = 'user_interaction';
    protected $_cols = array(
        'id', 'user_id_from', 'user_id_to',
        'time_update'
    );
    
    public function selectAllByUserIdTo($user_id)
    {
        $this->getTableGateway()->initialize();
        $sql = $this->getTableGateway()->getSql();
        $select = $sql->select();
        $select->where(array(
                'user_id_to'    =>  $user_id, 
                'user_id_from'  =>  $user_id), Predicate\PredicateSet::COMBINED_BY_OR
            )
            ->order('time_update DESC');
        return $select;
    }
    
    public function fetchByUserFromAndUserTo($user_id_from, $user_id_to)
    {
        $sql = $this->getTableGateway()->getSql();
        $select = $sql->select();
        $select->where(array(
            new Predicate\PredicateSet(
                array(
                    new Predicate\PredicateSet(
                        array(
                            new Predicate\Operator('user_id_from', '=', $user_id_from),
                            new Predicate\Operator('user_id_to', '=', $user_id_to),
                        ),
                        Predicate\PredicateSet::COMBINED_BY_AND
                    ),
                    new Predicate\PredicateSet(
                        array(
                            new Predicate\Operator('user_id_to', '=', $user_id_from),
                            new Predicate\Operator('user_id_from', '=', $user_id_to),
                        ),
                        Predicate\PredicateSet::COMBINED_BY_AND
                    )
                ),
                Predicate\PredicateSet::COMBINED_BY_OR
            ),
        ));
        return $this->getTableGateway()->selectWith($select)->current();
    }
}