<?php

namespace Game\Model\System\Paylog;

use Ap\Model\Table as Prototype;

class Table extends Prototype
{
    protected $_key = 'id';
    protected $_name = 'system_paylog';
    protected $_cols = array(
        'id', 'user_id', 'merchant', 'transaction', 'price', 'time_add', 
        'shop_id', 'response',
        'paystatus', 'errormessages', 'ip_address', 'country_code', 'receipt'
    );
    
    public function fetchByMerchantAndTransaction($merchant, $transaction)
    {
        return $this->getTableGateway()->select(array(
            'merchant'      =>  $merchant,
            'transaction'   =>  $transaction))->current();
    }
    
    public function add(Row $row)
    {
        $row->time_add = time();
        $row->save();
    }
    
    public function delete(Row $row)
    {
        $row->delete();
    }
}