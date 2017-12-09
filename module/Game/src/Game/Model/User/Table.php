<?php

namespace Game\Model\User;

use Ap\Model\Table as Prototype;

class Table extends Prototype
{
    protected $_name = 'user';
    protected $_cols = array(
        'id', 'email', 'ban_status', 'ban_chat_status', 'ban_chat_timeend',
        'time_add', 'time_last_connect', 'exp',
        'role', 'level', 'advice_time_update', 
        'activity_time_update','activity_rate', 'social_type', 'social_id', 'social_name',
        'social_link', 'referrer_id', 'countnotify', 'lastnotify', 'newsletter_time_send',
        'slot_time_update', 'slot_rate', 'slot_attempt'
    );
    protected $_counters = array(
    );
    
    protected $_defaults = array(
        'role'                  =>  'role',
        'ban_status'            =>  'off',
        'ban_chat_status'       =>  'off',
        'exp'                   =>  0,
        'level'                 =>  0,
        'activity_time_update'  =>  0,
        'activity_rate'         =>  0
    );
    
    public function fetchByEmail($email)
    {
        $this->getTableGateway()->initialize();
        $rowset = $this->getTableGateway()->select(array('email' => $email));
        return $rowset->current();
    }
    
    public function add(Row $userRow)
    {
        $userRow->setFromArray(array(
            'time_add'      =>  time(),
            'time_last_connect' =>  time(),
            'exp'           =>  0,
            'level'         =>  0,
        ));
        $userRow->save();
        
        $rowset = $this->getSm()->get('Lib\Object\Table')->getRowset();
        foreach($rowset->getItems() AS $libObjectRow){
            $objectRow = $this->getSm()->get('User\Object\Table')->createRow(array(
                'object_code'   =>  $libObjectRow->code,
                'user_id'       =>  $userRow->id,
                'count'         =>  $libObjectRow->default_count
            ));
            $objectRow->save();
        }
    }
    
    public function signup(Row $row)
    {
        $row->setFromArray(array(
            'role'              =>  'user',
            'ban_status'        =>  'off',
        ));
        $this->add($row);
    }
    
    public function edit(Row $row)
    {
        $row->save();
    }
    
    public function delete(Row $row)
    {
        $row->delete();
    }
}