<?php

namespace Api\Controller;

use Api\Controller\AbstractController;
use Game\InputFilter;
use Zend\Paginator;

class PokerController extends AbstractController
{
    public function indexAction()
    {
        
    }
    
    public function getRoomsAction()
    {
        $table = $this->getServiceLocator()->get('Poker\Table');
        $tableGateway = $table->getTableGateway()->get('slave');
        $sql = $tableGateway->getSql();
        $select = $sql->select();
        $select->order('blind ASC');
        
        $adapter = new \Ap\Paginator\Adapter\DbSelect($select, $tableGateway->getAdapter(), $tableGateway->getResultSetPrototype());
        
        $paginator = new Paginator\Paginator($adapter);
        $paginator->setCurrentPageNumber($this->params()->fromQuery('p',1))->setItemCountPerPage(20);
        
        return $this->outOk(array(), $paginator);
    }
    
    public function joinAction()
    {
        $filter = new InputFilter\Poker\Join($this->getServiceLocator());
        
        $transaction = $this->getServiceLocator()->get('Transaction');
        $transaction->setInputFilter($filter);
        $transaction->setDbs(array(
            'MultiServers'  =>  array('master')
        ));
        if ($transaction->request($this->params()->fromQuery())) {
            return $this->outOk(array(
                'money'     =>  $filter->getUserRow()->getMoneyCount(),
                'game_row'  =>  $filter->getGameRow()->toArrayForApi($this->getAuthUser())
            ));
        }
        return $this->outError($filter->getMessages());
    }
    
    public function enterAction()
    {
        $filter = new InputFilter\Poker\Enter($this->getServiceLocator());
        
        $transaction = $this->getServiceLocator()->get('Transaction');
        $transaction->setInputFilter($filter);
        $transaction->setDbs(array(
            'MultiServers'  =>  array('master')
        ));
        if ($transaction->request($this->params()->fromQuery())) {
            return $this->outOk(array(
                'game_row'  =>  $filter->getGameRow()->toArrayForApi($this->getAuthUser())
            ));
        }
        return $this->outError($filter->getMessages());
    }
    
    public function closeAction()
    {
        $filter = new InputFilter\Poker\Close($this->getServiceLocator());
        
        $transaction = $this->getServiceLocator()->get('Transaction');
        $transaction->setInputFilter($filter);
        $transaction->setDbs(array(
            'MultiServers'  =>  array('master')
        ));
        if ($transaction->request($this->params()->fromQuery())) {
            return $this->outOk(array(
                'money'     =>  $filter->getUserRow()->getMoneyCount(),
                'game_row'  =>  $filter->getGameRow()->toArrayForApi($this->getAuthUser())
            ));
        }
        return $this->outError($filter->getMessages());
    }
    
    public function leaveAction()
    {
        $filter = new InputFilter\Poker\Leave($this->getServiceLocator());
        
        $transaction = $this->getServiceLocator()->get('Transaction');
        $transaction->setInputFilter($filter);
        $transaction->setDbs(array(
            'MultiServers'  =>  array('master')
        ));
        if ($transaction->request($this->params()->fromQuery())) {
            return $this->outOk(array(
                'money'     =>  $filter->getUserRow()->getMoneyCount(),
                'game_row'  =>  $filter->getGameRow()->toArrayForApi($this->getAuthUser())
            ));
        }
        return $this->outError($filter->getMessages());
    }
    
    public function callAction()
    {
        $filter = new InputFilter\Poker\Call($this->getServiceLocator());
        
        $transaction = $this->getServiceLocator()->get('Transaction');
        $transaction->setInputFilter($filter);
        $transaction->setDbs(array(
            'MultiServers'  =>  array('master')
        ));
        if ($transaction->request($this->params()->fromQuery())) {
            return $this->outOk(array(
                'game_row'  =>  $filter->getGameRow()->toArrayForApi($this->getAuthUser())
            ));
        }
        return $this->outError($filter->getMessages());
    }
    
    public function checkAction()
    {
        $filter = new InputFilter\Poker\Check($this->getServiceLocator());
        
        $transaction = $this->getServiceLocator()->get('Transaction');
        $transaction->setInputFilter($filter);
        $transaction->setDbs(array(
            'MultiServers'  =>  array('master')
        ));
        if ($transaction->request($this->params()->fromQuery())) {
            return $this->outOk(array(
                'game_row'  =>  $filter->getGameRow()->toArrayForApi($this->getAuthUser())
            ));
        }
        return $this->outError($filter->getMessages());
    }
    
    public function foldAction()
    {
        $filter = new InputFilter\Poker\Fold($this->getServiceLocator());
        
        $transaction = $this->getServiceLocator()->get('Transaction');
        $transaction->setInputFilter($filter);
        $transaction->setDbs(array(
            'MultiServers'  =>  array('master')
        ));
        if ($transaction->request($this->params()->fromQuery())) {
            return $this->outOk(array(
                'game_row'  =>  $filter->getGameRow()->toArrayForApi($this->getAuthUser())
            ));
        }
        return $this->outError($filter->getMessages());
    }
    
    public function raiseAction()
    {
        $filter = new InputFilter\Poker\Raise($this->getServiceLocator());
        
        $transaction = $this->getServiceLocator()->get('Transaction');
        $transaction->setInputFilter($filter);
        $transaction->setDbs(array(
            'MultiServers'  =>  array('master')
        ));
        if ($transaction->request($this->params()->fromQuery())) {
            return $this->outOk(array(
                'game_row'  =>  $filter->getGameRow()->toArrayForApi($this->getAuthUser())
            ));
        }
        return $this->outError($filter->getMessages());
    }
    
    public function allinAction()
    {
        $filter = new InputFilter\Poker\AllIn($this->getServiceLocator());
        
        $transaction = $this->getServiceLocator()->get('Transaction');
        $transaction->setInputFilter($filter);
        $transaction->setDbs(array(
            'MultiServers'  =>  array('master')
        ));
        if ($transaction->request($this->params()->fromQuery())) {
            return $this->outOk(array(
                'game_row'  =>  $filter->getGameRow()->toArrayForApi($this->getAuthUser())
            ));
        }
        return $this->outError($filter->getMessages());
    }
    
}

