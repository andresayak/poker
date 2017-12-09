<?php

namespace Game\Model\Chat;

class Collection extends \Ap\MongoDb\Collection
{
    protected $name = 'chat';
    
    protected $_defaults = array(
        'message', 'user_id', 'time_send', 'alliance_id', 'type',
        'region_id', 'color', 'link'
    );
    
    protected $_datatypes = array(
    );
    
    public function findByAllianceId($alliance_id)
    {
        return $this->findAllBy(array(
            'type'  =>  'alliance',
            'alliance_id'   =>  $alliance_id
        ));
    }
    
    public function findAllByType($type)
    {
        $cursor = $this->findBy(array(
            'type'  =>  $type
        ))->sort(array(
            'time_send'  =>  1
        ));
        $result = array();
        foreach ($cursor as $doc) {
            $result[] = $doc;
        }
        $entitySet = clone $this->createEntitySetPrototype();
        return $entitySet->exchangeArray($result);
    }
    
    public function findLastByType($type, $limit)
    {
        $cursor = $this->findBy(array(
            'type'  =>  $type
        ))->sort(array(
            '_id'  =>  -1
        ))->skip($limit);
        $result = array();
        foreach ($cursor as $doc) {
            $result[] = $doc;
        }
        $entitySet = clone $this->createEntitySetPrototype();
        return $entitySet->exchangeArray($result);
    }
    
    public function findLastAllByAllianceId($alliance_id, $limit)
    {
        $cursor = $this->findBy(array(
            'type'  =>  'alliance',
            'alliance_id'   =>  $alliance_id
        ))->sort(array(
            '_id'  =>  -1
        ))->skip($limit);
        $result = array();
        foreach ($cursor as $doc) {
            $result[] = $doc;
        }
        $entitySet = clone $this->createEntitySetPrototype();
        return $entitySet->exchangeArray($result);
    }
}

