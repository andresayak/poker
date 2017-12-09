<?php

namespace Control\Controller;

use Ap\Controller\AbstractController;
use Zend\Http\Request;
use Zend\Http\Client;
use Zend\Console\Console;
use Game\Model\Poker\Exception\ExceptionRule;

class PokerController extends AbstractController
{
    public function indexAction()
    {
        $gameTable = $this->getTable('Poker');
        $gameRowset = $gameTable->fetchAll();
        foreach($gameRowset->getItems() AS $game){
            print_r($game->toArray());
        }
    }
    
    public function createAction()
    {
        $gameTable = $this->getTable('Poker');
        foreach(array(4, 6, 8, 10) AS $max_player){
            foreach(array(1, 10, 50, 100, 200, 500, 1000) AS $blind){
                $gameRow = $gameTable->createRow(array(
                    'max_players'   =>  $max_player,
                    'blind'         =>  $blind
                ));
                $gameRow->save();
            }
        }
        return "Done!\n";
    }
    
    public function timeoutAction()
    {
        $id = $this->params()->fromRoute('id');
        $gameTable = $this->getTable('Poker');
        if(!$id or !$gameRow = $gameTable->fetchBy('id', $id)){
            return "Game not found\n";
        }
        $gameRow->enableLogger();
        $result = $gameRow->timeout();
        $transaction = $this->getServiceLocator()->get('Transaction');
        $transaction->setDbs(array(
            'MultiServers'  =>  array('master')
        ));
        if($result){
            $userRow = $this->getTable('User')->fetchBy('id', $result['user_id']);
            if($userRow){
                $transaction->run(function() use ($userRow, $result){
                    $userRow->updateObjectRow('chip', $result['money']);
                });
            }
        }
        return "Done!\n";
    }
    
    public function startAction()
    {
        $id = $this->params()->fromRoute('id');
        $gameTable = $this->getTable('Poker');
        if(!$id or !$gameRow = $gameTable->fetchBy('id', $id)){
            return "Game not found\n";
        }
        if(!$gameRow->getGame()->ifHavePlayers()){
            return "Game not have players for normal work\n";
        }else{
            $gameRow->enableLogger();
            $gameRow->start();
        }
        
        return "Done!\n";
    }
    
    public function infoAction()
    {
        $id = $this->params()->fromRoute('id');
        $gameTable = $this->getTable('Poker');
        if(!$id or !$gameRow = $gameTable->fetchBy('id', $id)){
            return "Game not found\n";
        }
        $gameRow->enableLogger();
        print_r($gameRow->toArrayForApi());
        
        return "Done!\n";
    }
    
    public function joinAction()
    {
        $id = $this->params()->fromRoute('id');
        $gameTable = $this->getTable('Poker');
        if(!$id or !$gameRow = $gameTable->fetchBy('id', $id)){
            return "Game not found\n";
        }
        
        $position = $this->params()->fromRoute('position');
        if(!$position){
            return 'Position invalid'."\n";
        }
        
        $money = $this->params()->fromRoute('money');
        if(!$money){
            return 'Money invalid'."\n";
        }
        
        $user_id = $this->params()->fromRoute('user_id');
        if(!$user_id or !$userRow = $this->getTable('User')->fetchBy('id', $user_id)){
            return 'User invalid'."\n";
        }
        $gameRow->enableLogger();
        try {
            $gameRow->addUser($userRow, $position, $money);
        } catch (\Game\Model\Poker\Exception\SeatBusy $exc) {
            return $exc->getMessage()."\n";
        } catch (\Game\Model\Poker\Exception\UserArleadyInGame $exc){
            return $exc->getMessage()."\n";
        }
        return "Done!\n";
    }
    
    public function leaveAction()
    {
        $id = $this->params()->fromRoute('id');
        $gameTable = $this->getTable('Poker');
        if(!$id or !$gameRow = $gameTable->fetchBy('id', $id)){
            return "Game not found\n";
        }
        
        $user_id = $this->params()->fromRoute('user_id');
        if(!$user_id or !$userRow = $this->getTable('User')->fetchBy('id', $user_id)){
            return 'User invalid'."\n";
        }
        $gameRow->enableLogger();
        try {
            $gameRow->removeUser($userRow);
        } catch (ExceptionRule $exc) {
            return $exc->getMessage()."\n";
        }
        return "Done!\n";
    }
    
    public function raiseAction()
    {
        $id = $this->params()->fromRoute('id');
        $gameTable = $this->getTable('Poker');
        if(!$id or !$gameRow = $gameTable->fetchBy('id', $id)){
            return "Game not found\n";
        }
        
        $user_id = $this->params()->fromRoute('user_id');
        if(!$user_id or !$userRow = $this->getTable('User')->fetchBy('id', $user_id)){
            return 'User invalid'."\n";
        }
        $money = $this->params()->fromRoute('money');
        if(!$money){
            return 'Money invalid'."\n";
        }
        $gameRow->enableLogger();
        try {
            $gameRow->raise($userRow, $money);
        } catch (ExceptionRule $exc) {
            return $exc->getMessage()."\n";
        }
        return "Done!\n";
    }
    
    public function allinAction()
    {
        $id = $this->params()->fromRoute('id');
        $gameTable = $this->getTable('Poker');
        if(!$id or !$gameRow = $gameTable->fetchBy('id', $id)){
            return "Game not found\n";
        }
        
        $user_id = $this->params()->fromRoute('user_id');
        if(!$user_id or !$userRow = $this->getTable('User')->fetchBy('id', $user_id)){
            return 'User invalid'."\n";
        }
        $gameRow->enableLogger();
        try {
            $gameRow->allin($userRow);
        } catch (ExceptionRule $exc) {
            return $exc->getMessage()."\n";
        }
        return "Done!\n";
    }
    
    public function callAction()
    {
        $id = $this->params()->fromRoute('id');
        $gameTable = $this->getTable('Poker');
        if(!$id or !$gameRow = $gameTable->fetchBy('id', $id)){
            return "Game not found\n";
        }
        
        $user_id = $this->params()->fromRoute('user_id');
        if(!$user_id or !$userRow = $this->getTable('User')->fetchBy('id', $user_id)){
            return 'User invalid'."\n";
        }
        $gameRow->enableLogger();
        try {
            $gameRow->call($userRow);
        } catch (ExceptionRule $exc) {
            return $exc->getMessage()."\n";
        }
        return "Done!\n";
    }
    
    public function checkAction()
    {
        $id = $this->params()->fromRoute('id');
        $gameTable = $this->getTable('Poker');
        if(!$id or !$gameRow = $gameTable->fetchBy('id', $id)){
            return "Game not found\n";
        }
        
        $user_id = $this->params()->fromRoute('user_id');
        if(!$user_id or !$userRow = $this->getTable('User')->fetchBy('id', $user_id)){
            return 'User invalid'."\n";
        }
        $gameRow->enableLogger();
        try {
            $gameRow->check($userRow);
        } catch (ExceptionRule $exc) {
            return $exc->getMessage()."\n";
        }
        return "Done!\n";
    }
    
    public function foldAction()
    {
        $id = $this->params()->fromRoute('id');
        $gameTable = $this->getTable('Poker');
        if(!$id or !$gameRow = $gameTable->fetchBy('id', $id)){
            return "Game not found\n";
        }
        
        $user_id = $this->params()->fromRoute('user_id');
        if(!$user_id or !$userRow = $this->getTable('User')->fetchBy('id', $user_id)){
            return 'User invalid'."\n";
        }
        $gameRow->enableLogger();
        try{
            $gameRow->fold($userRow);
        } catch (ExceptionRule $exc) {
            return $exc->getMessage()."\n";
        }
        return "Done!\n";
    }
    
    public function statusAction()
    {
        $id = $this->params()->fromRoute('id');
        $gameTable = $this->getTable('Poker');
        if(!$id or !$gameRow = $gameTable->fetchBy('id', $id)){
            return "Game not found\n";
        }
        return $gameRow->getGame()->status()."\n";
    }
    
    public function clearAction()
    {
        $id = $this->params()->fromRoute('id', false);
        $gameTable = $this->getTable('Poker');
        if($id){
            if(!$gameRow = $gameTable->fetchBy('id', $id)){
                return "Game not found\n";
            }
            $gameRow->clear();
        }else{
            $gameRowset = $gameTable->fetchAll();
            foreach($gameRowset->getItems() AS $gameRow){
                $gameRow->clear();
            }
        }
        
        return "Done!\n";
    }    
}
   