<?php

namespace Game\Model\System\Server;

use Ap\Model\Table as Prototype;

class Table extends Prototype
{
    protected $_key = 'code';
    protected $_name = 'system_server';
    protected $_cols = array(
        'code', 'ip_external_address', 'ip_network_address', 'status_data'
    );
    
    protected $_defauls = array(
        'status_data'   =>  null
    );
    public function add(Row $row)
    {
        $row->save();
    }
}