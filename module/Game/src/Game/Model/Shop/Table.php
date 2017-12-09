<?php

namespace Game\Model\Shop;

use Ap\Model\Table as Prototype;

class Table extends Prototype
{
    protected $_name = 'shop';
    protected $_cols = array(
        'id', 'price', 'price_old', 'position', 'title', 'discount',
        'icon_filename', 'translate_code', 'type', 'appstore_id', 'event_id',
        'auth_type', 'promotype', 'theme', 'info_count', 'info_bonus', 'promo_period'
    );
    
    protected $_defaults = array(
        'appstore_id'   =>  null,
        'event_id'      =>  null,
        'discount'      =>  null,
        'promotype'     =>  null,
        'auth_type'     =>  null,
        'theme'         =>  null,
        'info_count'         => null,
        'info_bonus'         => null,
        'promo_period'       => null,
    );

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