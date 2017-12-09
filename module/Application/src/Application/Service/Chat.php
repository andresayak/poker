<?php

namespace Application\Service;

class Chat
{
    protected $_sm;
    const MAX = 100;
    
    public function __construct($sm)
    {
        $this->_sm = $sm;
        return $this;
    }
    
    public function getSm()
    {
        return $this->_sm;
    }
    
    public function addToRoom($data, $room_id)
    {
        $redis = $this->getSm()->get('Chat\Cache\Storage');
        $key = 'room'.$room_id.'Messages';
        $redis->lPush($key, $data);
        //$data['serviceName'] = 'Poker\Cache\Storage';
        $this->getSm()->get('PushCommet\Service')->send($data, 'room', null, $room_id);
        $len = $redis->lLen($key);
        if($len>=100){
            $redis->rPop($key);
        }
    }
    
    public function addToPublic($data)
    {
        $redis = $this->getSm()->get('Chat\Cache\Storage');
        $key = 'publicMessages';
        $redis->lPush($key, $data);
        $data['serviceName'] = 'Chat\Cache\Storage';
        $this->getSm()
            ->get('PushCommet\Service')->send($data, 'public');
        $len = $redis->lLen($key);
        if($len>=100){
            $redis->rPop($key);
        }
    }
    
    public function reset()
    {
        $collection = $this->getSm()->get('Chat\Collection');
        $collection->removeBy();
    }
    
    public function getPublic()
    {
        $cache = $this->getSm()->get('Chat\Cache\Storage');
        $rowset = new \Game\Model\Chat\EntitySet();
        $rowset->setSm($this->getSm());
        $rowset->setEntityPrototype(new \Game\Model\Chat\Entity);
        $result = $cache->lRange('publicMessages', 0, 99);
        $list = array();
        foreach($result AS $item){
            $list[] = $item[0];
        }
        return $rowset->exchangeArray($list);
    }
    
    public function getByRoomId($room_id)
    {
        $cache = $this->getSm()->get('Chat\Cache\Storage');
        $rowset = new \Game\Model\Chat\EntitySet();
        $rowset->setSm($this->getSm());
        $rowset->setEntityPrototype(new \Game\Model\Chat\Entity);
        $result = $cache->lRange('room'.$room_id.'Messages', 0, 99);
        $list = array();
        foreach($result AS $item){
            $list[] = $item[0];
        }
        return $rowset->exchangeArray($list);
    }
}