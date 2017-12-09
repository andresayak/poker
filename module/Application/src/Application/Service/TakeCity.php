<?php
namespace Application\Service;

use Zend\Session\Container;

class TakeCity 
{
    protected $_city_row, $_session;
    protected $_auth;
    
    public function getSession()
    {
        if(null === $this->_session){
            $this->_session =  new Container('TakeCity');
        }
        return $this->_session;
    }
    
    public function getAuthService()
    {
        if(null === $this->_auth){
            throw new \Exception('AuthService not set');
        }
        return $this->_auth;
    }
    
    public function setAuthService($auth)
    {
        $this->_auth = $auth;
        return $auth;
    }
    
    public function take($id)
    {
        $this->getSession()->id = $id;
        $this->_city_row = null;
        return $this;
    }
    
    public function getCityRow()
    {
        if($this->_city_row === null){
            if($this->getAuthService()->getUserRow()){
                if(!isset($this->getSession()->id) or !$this->getSession()->id or !$this->getAuthService()->getUserRow()->getCityRowset()->getBy('id', $this->getSession()->id)){
                    $id = $this->getAuthService()->getUserRow()->getCityRowset()->minValue('id');
                    $this->take($id);
                }
                $this->_city_row = $this->getAuthService()->getUserRow()->getCityRowset()->getBy('id', $this->getSession()->id);
            }
        }
        return $this->_city_row;
    }
}