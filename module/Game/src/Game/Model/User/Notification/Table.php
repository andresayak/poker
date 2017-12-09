<?php

namespace Game\Model\User\Notification;

use Ap\Model\Table as Prototype;
use \Zend\Db\Sql\Expression;

class Table extends Prototype
{
    protected $_name = 'user_notification';
    protected $_cols = array(
        'id', 'arguments', 'template',
        'user_id', 'time_create', 'time_read'
    );
    
    public function fetchAllNoreadByUserId($user_id)
    {
        $this->getTableGateway()->initialize();
        return $this->getTableGateway()->select(array(
            'user_id' => $user_id, 
            'time_read IS NULL'));
    }
    
    public function fetchAllMostDelete()
    {
        $this->getTableGateway()->initialize();
        return $this->getTableGateway()->select(array(
            'time_create <= ?'=>  strtotime('-1 month')));
    }
    
    public function add(Row $row)
    {
        $row->time_create = time();
        $row->save();
        
        $row->getUserRow()->notification_count++;
        $row->getUserRow()->save();
        
        /*$this->getSm()->get('PushCommet\Service')->sendToUser(array(
            'notification'  =>  1, 
            'type'          =>  'notification',
            'message'       =>  $row->getTemplateRow()->subject
        ), $row->getUserRow());*/
    }
    
    public function read(Row $row) 
    {
        if (!$row->time_read) {
            $row->time_read = time();
            $row->save();

            $row->getUserRow()->notification_count--;
            $row->getUserRow()->save();
        }
    }
    
    public function delete(Row $row) 
    {
        $row->delete();
        if (!$row->time_read) {
            $row->getUserRow()->notification_count--;
            $row->getUserRow()->save();
        }
    }
}