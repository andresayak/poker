<?php

namespace Game\InputFilter\Shop;

use Game\InputFilter\InputFilter;
use Game\Model\Shop\Row AS ShopRow;

class BuyFacebook extends InputFilter
{
    protected $_shop_row, $_count;
    protected $_response, $_receipt;
    public function __construct($sm)
    {
        $this->_sm = $sm;
        $this->add(array(
            'name' => 'count',
            'required' => true,
        ));
        
        $this->add(array(
            'name'  =>  'app_id',
            'required' => true,
            'validators'    =>  array(
                array(
                    'name' => 'facebookAppId',
                    'break_chain_on_failure' => true
                ),
            )
        ));
        
        $this->get('app_id')->setbreakOnFailure(true);
        
        $this->add(array(
            'name'  =>  'item_id',
            'required' => true,
            'validators'    =>  array(
                array(
                    'name'  =>  'checkFetchRow',
                    'options'   =>  array(
                        'callback'  => array($this, 'setShopRow'),
                        'table'     =>  'Shop',
                        'key'       =>  'id'
                    ),
                    'break_chain_on_failure'=>true
                ),
            )
        ));
        
        $this->get('item_id')->setbreakOnFailure(true);
        
        $this->add(array(
            'name'  =>  'item_price',
            'required' => true,
            'validators'    =>  array(
                array(
                    'name' => 'facebookShopPrice',
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
                        'key'       =>  'social_id'
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
        if($this->getShopRow()){
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
                    'user_id'       =>  (($this->getUserRow())?$this->getUserRow()->id:null),
                    'merchant'      =>  'fb',
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
        $this->getShopRow()->buy($this->getUserRow(), $this->getCount());
    }
    
    public function checkPreData($getData)
    {
        if(!isset($getData['payment_id'])){
            return array();
        }
        $config = $this->getSm()->get('config');
        if(isset($config['testAuth']) 
            && isset($getData['user_id']) 
            && in_array($getData['user_id'], $config['testAuth'])
        ){
            return $getData;
        }
        $log = $this->getSm()->get('Pay\Log');
        $data = array();
        $config = $this->getSm()->get('config');
        $command = 'curl '
            . '  "https://graph.facebook.com/oauth/access_token?'
            . 'client_id=' . $config['facebook']['app_id']
            . '&client_secret=' . $config['facebook']['key']
            . '&grant_type=client_credentials"';
        $out = exec($command);
        $response = json_decode($out, true);
        $file = './data/logs/paylog_' . date('Y-m-d') . '_keys.txt';
        file_put_contents($file, print_r($out, true) . "\n\n\n\n\n");
        if(!isset($response['access_token'])){
            $file = './data/logs/error_paylog_' . date('Y-m-d') . '.txt';
            file_put_contents($file, print_r($out, true) . "\n\n\n\n\n");
            throw new \Exception('invalid facebook output ['.$out.']');
        }
        $command = 'curl "https://graph.facebook.com/'.$config['facebook']['ver']
            . '/' . $getData['payment_id'] . '?access_token=' . $response['access_token'] 
            . '&fields=actions,items,application,user"';

        $out = exec($command);
        $response = json_decode($out, true);
        if(isset($response['error'])){
            $log->err('invalid facebook response error'."\n".print_r($out, true));
            return $response;
        }
        if ($response
                and isset($response['actions'])
                and isset($response['actions'][0])
                and isset($response['actions'][0]['status'])
                and $response['actions'][0]['status'] == 'completed'
                and isset($response['items'])
                and isset($response['items'][0])
        ) {
            if (preg_match('/shop\.getInfo\?id=(\d+)/', $response['items'][0]['product'], $match)) {
                $data = array(
                    'app_id' => $response['application']['id'],
                    'item_id' => $match[1],
                    'count' => $response['items'][0]['quantity'],
                    'item_price' => $response['actions'][0]['amount'],
                    'user_id' => $response['user']['id'],
                    'status' => $response['actions'][0]['status'],
                    'order_id' => $response['id'],
                    'response' => $response,
                );
            }
        } else {
            $file = './data/logs/paylog_' . date('Y-m-d') . '_response.txt';
            file_put_contents($file, $command . "\n\n" . print_r($response, true) . "\n\n\n\n\n");
        }
        return $data;
    }
}
