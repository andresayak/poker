<?php

namespace Game\Validator;

use Zend\Stdlib\PriorityQueue;

class ValidatorChain extends \Zend\Validator\ValidatorChain
{
    protected $_results, $_sm, $_filter;
    protected $invokableClasses = array(
        'ver'                   =>  'Game\Validator\System\Version',
        'checkBan'              =>  'Game\Validator\System\CheckBan',
        'checkAuthRole'         =>  'Game\Validator\CheckAuthRole',
        'checkAuth'             =>  'Game\Validator\CheckAuth',
        'checkAcl'              =>  'Game\Validator\CheckAcl',
        'checkGetRow'           =>  'Game\Validator\CheckGetRow',
        'checkFetchRow'         =>  'Game\Validator\CheckFetchRow',
        'checkAccess'           =>  'Game\Validator\CheckAccess',
        'shopPromo'                 =>  'Game\Validator\ShopPromo',
        'checkChatBan'              =>  'Game\Validator\CheckChatBan',
        'checkChatStoplist'         =>  'Game\Validator\CheckChatStoplist',
        
        'uidNotUsed'                =>  'Game\Validator\UidNotUsed',
        
        'username'              =>  'Game\Validator\Username',
        'needsSingle'   =>  'Game\Validator\ValidNeeds\Single',
        'needsItem'   =>  'Game\Validator\ValidNeeds\Item',
        
        'mailruCheckAccess'   =>  'Game\Validator\Mailru\CheckAccess',
        
        'facebookCheckAccess'   =>  'Game\Validator\Facebook\CheckAccess',
        'facebookAppId'   =>  'Game\Validator\Facebook\AppId',
        'facebookShopPrice' =>  'Game\Validator\Facebook\ShopPrice',
        
        'vkontakteCheckAccess'   =>  'Game\Validator\Vkontakte\CheckAccess',
        'vkontakteAppId'   =>  'Game\Validator\Vkontakte\AppId',
        'vkontakteShopItem'   =>  'Game\Validator\Vkontakte\ShopItem',
        'vkontakteShopPrice' =>  'Game\Validator\Vkontakte\ShopPrice',
        'payTransactionId'  =>  'Game\Validator\PayTransactionId',
        
        'Poker\CheckServer'  =>  'Game\Validator\Poker\CheckServer',
        'Poker\CheckSeat'  =>  'Game\Validator\Poker\CheckSeat',
        'Poker\CheckStep'  =>  'Game\Validator\Poker\CheckStep',
        'Poker\CheckMoney'  =>  'Game\Validator\Poker\CheckMoney',
        'Poker\CheckListen'  =>  'Game\Validator\Poker\CheckListen',
    );
    
    public function __construct() 
    {
        $this->validators = new PriorityQueue();
        
        foreach($this->invokableClasses AS $name=>$path){
            $this->getPluginManager()->setInvokableClass($name, $path);
        }
    }
    
    public function plugin($name, array $options = null)
    {
        $plugins = $this->getPluginManager();
        $validator = $plugins->get($name, $options);
        if($validator instanceof AbstractValidator){
            $validator->setFilter($this->getFilter());
        }
        return $validator;
    }
    
    public function getFilter()
    {
        return $this->_filter;
    }
    
    public function setFilter($filter)
    {
        $this->_filter = $filter;
        return $this;
    }
}
