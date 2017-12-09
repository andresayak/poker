<?php

namespace Api\Controller;

use Game\View\Model\JsonModel;
use Api\Controller\AbstractController;
use Game\InputFilter;

class ShopController extends AbstractController
{
    function getListAction() 
    {
        $data = array();
        $data['list'] = $this->getServiceLocator()->get('Shop\Table')->getRowset()->toArrayForApi();
        return $this->outOk($data);
    }
    
    public function cancelAction()
    {
        return $this->outOk();
    }
    
    public function callbackAction()
    {
        $data = $this->params()->fromPost()+$this->params()->fromQuery();
        
        if(AUTH_TYPE == 'fb'){
            $getData = $this->params()->fromQuery();
            if(isset($getData['hub_mode']) && $getData['hub_mode']=='subscribe' && isset($getData['hub_challenge'])){
                echo $getData['hub_challenge'];exit;
            }
            $filter = new InputFilter\Shop\BuyFacebook($this->getServiceLocator());
            $data = $filter->checkPreData($getData);
            if(isset($data['error'])){
                return $this->outError($data['error']);
            }
        }elseif(AUTH_TYPE == 'ok'){
            $data = $this->params()->fromQuery();
            if(isset($data['method']) && $data['method']=='callbacks.payment'){
                if (array_key_exists("product_code", $data) 
                    && array_key_exists("amount", $data) 
                    && array_key_exists("sig", $data)
                ){
                    header('Content-Type: application/xml');
                    echo '<?xml version="1.0" encoding="UTF-8"?>'
                        .'<callbacks_payment_response xmlns="http://api.forticom.com/1.0/">'
                        .'true'
                        .'</callbacks_payment_response>';
                }else{
                    header('Content-Type: application/xml');
                    header('invocation-error: 1001');
                    
                    echo '<?xml version="1.0" encoding="UTF-8"?>'
                        .'<ns2:error_response xmlns:ns2=\'http://api.forticom.com/1.0/\'>'
                            .'<error_code>1001</error_code>'
                            .'<error_msg>CALLBACK_INVALID_PAYMENT : Payment is invalid and can not be processed</error_msg>'
                        .'</ns2:error_response>';
                }
                exit;
            }
        }elseif(AUTH_TYPE == 'vk'){
            $data = $this->params()->fromPost();
            
            $filter = new InputFilter\Shop\BuyVkontakte($this->getServiceLocator());
            $result = $filter->checkPreData($data);
            if(isset($result['error'])){
                $response = $this->getResponse();
                $response->setStatusCode(200);
                return $this->out($result);
            }
            if(isset($result['response'])){
                return $this->out($result);
            }
        }else{
            $filter = new InputFilter\Shop\Buy($this->getServiceLocator());
        }
                
        if(isset($filter)){
            $transaction = $this->getServiceLocator()->get('Transaction');
            $transaction->setInputFilter($filter);
            $transaction->setDbs(array('MultiServers'=>array('master')));
            if ($transaction->request($data)) {
                $filter->savelog(true);
                if(AUTH_TYPE == 'vk'){
                    return new JsonModel(array(
                        'response'=>array(
                            'order_id'   =>  $filter->getValue('order_id'), 
                            'app_order_id'     =>  time()
                        )
                    ));
                }else{
                    return $this->outOk(array(
                        'objectList' =>  $filter->getUserRow()->getObjectRowset()->toArrayForApi(),
                    ));
                }
            }
            $filter->savelog(false);
            return $this->outError($filter->getMessages());
        }
        return $this->outError('other error');
    }
    
    public function getInfoAction()
    {
        $config = $this->getServiceLocator()->get('config');
        $shopRow = $this->getTable('shop')->fetchBy('id', $this->params()->fromQuery('id'));
        if($shopRow){
            $lang = $this->params()->fromQuery('lang', 'en');
            if(!preg_match('/^(\w{2})$/i', $lang)){
                $lang = 'en';
            }
            $translator = $this->getServiceLocator()->get('translator');
            $translator->setLocale($lang);
            $basePath = 'http'.((isset($_SERVER['HTTPS']) && $_SERVER['HTTPS']=='on')?'s':'').'://'.$_SERVER['HTTP_HOST'].'/';

            echo '<head prefix="og: http://ogp.me/ns# fb: http://ogp.me/ns/fb#">
            <meta property="og:type" content="og:product" />
            <meta property="og:title" content="'.$shopRow->title.'" />
            <meta property="og:plural_title" content="'.$shopRow->title.'" />
            <meta property="og:image" content="'.$basePath.'img/shop/'.$shopRow->icon_filename.'_75x75.png'.'" />
            <meta property="og:url" content="'.$basePath.'api/shop.getInfo?id='.$shopRow->id.'&lang='.$lang.'" />
            <meta property="og:description" content="" />
            <meta property="product:price:amount" content="'.$shopRow->price.'"/>
            <meta property="product:price:currency" content="USD"/>
          </head>';    
        }
        exit;
    }
}
