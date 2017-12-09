<?php

namespace Game\Model\Lib\Notification;

trait Provider {

    use \Game\Provider\ProvidesServiceManager;
    
    protected $_notification_row;

    public function getNotificationRow() 
    {
        if($this->_notification_row === null){
            $this->_notification_row = $this->getSm()->get('Lib\Notification\Table')->fetchBy('code', $this->notification_code);
        }
        return $this->_notification_row;
    }

    public function setNotificationRow(Row $notificationRow) 
    {
        $this->_notification_row = $notificationRow;
        return $this;
    }
}
