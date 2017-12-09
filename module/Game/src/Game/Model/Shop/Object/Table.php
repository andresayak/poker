<?php

namespace Game\Model\Shop\Object;

use Ap\Model\Table as Prototype;

class Table extends Prototype
{
    protected $_name = 'shop_object';
    protected $_cols = array(
        'id', 'shop_id', 'object_code', 
        'count'
    );
    
    public function fetchAllByShopId($id) 
    {
        return $this->getTableGateway()->select(array('shop_id'=>$id));
    }
    
    public function add(Row $row)
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