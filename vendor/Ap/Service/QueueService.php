<?php

namespace Ap\Service;

class QueueService
{
    protected $_name = 'QueueList';
    
    public function getCache()
    {
        return $this->_cache;
    }
    
    public function setCache($cache)
    {
        $this->_cache = $cache;
        return $this;
    }
    
    public function pop($prefix='')
    {
        return $this->getCache()->getConnection()->rPop($this->_name.$prefix);
    }

    public function push($prefix='', $data)
    {
        return $this->getCache()->getConnection()->lPush($this->_name.$prefix, $data);
    }
    
}
