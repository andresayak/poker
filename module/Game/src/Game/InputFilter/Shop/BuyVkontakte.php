<?php

namespace Game\InputFilter\Shop;

use Game\InputFilter;
use Game\Model\Shop\Row AS ShopRow;

class BuyVkontakte extends InputFilter
{
    protected $_shop_row, $_count;
    protected $_response, $_receipt;
    public function __construct($sm)
    {
        $this->_sm = $sm;
        $this->add(array(
            'name'  =>  'app_id',
            'required' => true,
            'validators'    =>  array(
                array(
                    'name' => 'vkontakteAppId',
                    'break_chain_on_failure' => true
                ),
            )
        ));
        
        $this->get('app_id')->setbreakOnFailure(true);
        
        $this->add(array(
            'name'  =>  'item',
            'required' => true,
            'validators'    =>  array(
                array(
                    'name' => 'vkontakteShopItem',
                    'break_chain_on_failure' => true
                ),
            )
        ));
        
        $this->get('item')->setbreakOnFailure(true);
        
        $this->add(array(
            'name'  =>  'item_price',
            'required' => true,
            'validators'    =>  array(
                array(
                    'name' => 'vkontakteShopPrice',
                    'break_chain_on_failure' => true
                ),
            )
        ));
        
        $this->get('item_price')->setbreakOnFailure(true);
        
        $this->add(array(
            'name' => 'user_id',
            'required' => true,
            'filters' => array(
                array('name' => 'StripTags'),
                array('name' => 'StringTrim'),
            ),
            'validators' => array(
                array(
                    'name'  =>  'checkFetchRow',
                    'options'   =>  array(
                        'callback'  => array($this, 'setUserRow'),
                        'table'     =>  'User',
                        'key'       =>  'vkontakte_user_id'
                    ),
                    'break_chain_on_failure'=>true
                ),
            )
        ));
        
        $this->get('user_id')->setbreakOnFailure(true);
        
        $this->add(array(
            'name' => 'status',
            'required' => true,
        ));
        
        $this->add(array(
            'name'  =>  'order_id',
            'required'  =>  true,
            'validators'    =>  array(
                array(
                    'name'    =>  'payTransactionId',
                )
            )
        ));
    }
    
    public function savelog($status)
    {
        if($this->getShopRow() && $this->getShopRow()->isBuyGem()){
            $config = $this->getSm()->get('config');
            $payLogTable = $this->getSm()->get('System\Paylog\Table');
            $messagesStr = '';
            foreach($this->getMessages() AS $col=>$messages){
                foreach($messages AS $message){
                    $messagesStr.= $col.': '.$message."\n";
                }
            }
            if(function_exists('geoip_country_code_by_name')){
                $country_code = \geoip_country_code_by_name(IP_ADDRESS);
            }else{
                $country_code = null;
            }
            try {
                $payLogRow = $payLogTable->createRow(array(
                    'server_id'     =>  SERVER_ID,
                    'user_id'       =>  (($this->getUserRow())?$this->getUserRow()->id:null),
                    'merchant'      =>  'vk',
                    'shop_id'       =>  (($this->_shop_row)?$this->getShopRow()->id:null),
                    'transaction'   =>  $this->getValue('order_id'),
                    'price'         =>  (($this->_shop_row)?$this->getShopRow()->price * $this->getCount():null),
                    'response'      =>  print_r($this->getValues(), true),
                    'paystatus'     =>  $status,
                    'errormessages' =>  $messagesStr,
                    'ip_address'    =>  ip2long(IP_ADDRESS),
                    'country_code'  =>  $country_code,
                    'receipt'       =>  ''
                ));
                $payLogTable->add($payLogRow);
            } catch (\Exception $e) {
            }
        }
    }
    
    public function getCount()
    {
        if($this->_count === null){
            $this->_count = $this->getValue('count');
            $this->_count = (int)($this->_count)?$this->_count:1;
        }
        return  $this->_count;
    }
    
    public function setCount($count)
    {
        $this->_count = $count;
    }
    
    public function getShopRow()
    {
        return $this->_shop_row;
    }
    
    public function setShopRow(ShopRow $row)
    {
        $this->_shop_row = $row;
        return $this;
    }
    
    public function finish()
    {
        $this->getShopRow()->buy($this->getCityRow(), $this->getCount());
    }
    
    public function checkPreData($data)
    {
        $config = $this->getSm()->get('config');
        if (isset($data['notification_type'])) {
            if ($data['notification_type'] == 'get_item_test'
                    or $data['notification_type'] == 'get_item'
            ) {
                $langs = explode('_', $data['lang']);
                $translator = $this->getSm()->get('translator');
                $translator->setLocale($langs[0]);

                $itemCount = explode('_', $data['item']);
                $order_id = $itemCount[0];
                $count = (int) $itemCount[1];

                $orderRow = $this->getSm()->get('Shop\Table')->fetchBy('id', $order_id);
                if (!$orderRow) {
                    return $this->_outVkError('not found shopRow');
                }
                if (!$count > 0) {
                    return $this->_outVkError('invalid count');
                }
                $userRow = $this->getSm()->get('User\Table')->fetchBy('vkontakte_user_id', $data['user_id']);
                if ($config['vk']['app_id'] != $data['app_id']) {
                    return $this->_outVkError('invalid app_id');
                }
                $str = '';
                ksort($data);
                foreach ($data as $k => $v) {
                    if ($k != 'sig')
                        $str.= $k . '=' . $v;
                }
                if ($data['sig'] != md5($str . $config['vk']['key'])) {
                    return $this->_outVkError('invalid app_id');
                }

                $basePath = $config['view_manager']['base_path'];
                return array(
                    'response' => array(
                        'item_id' => $data['item'],
                        'title' => $translator->translate('shop.' . $orderRow->translate_code) . (($count > 1) ? ' [' . $count . ']' : ''),
                        'photo_url' => $basePath . 'img/shop/' . $orderRow->icon_filename . '_75x75.png',
                        'price' => ceil($config['vk']['price_rate'] * $orderRow->price) * $count
                    )
                );
            } elseif ($data['notification_type'] == 'order_status_change'
                or $data['notification_type'] == 'order_status_change_test'
            ) {
                return true;
            }
        }
        
    }
    
    protected function _outVkError($errorMsg)
    {
        return array('error'=>array(
            'error_code' => 20, 
            'error_msg' => $errorMsg, 
            'critical' => true 
        ));
    }
}
