<?php

namespace Game\InputFilter\Poker;

use Game\InputFilter\InputFilter;

use Game\Model\Poker\Row AS GameRow;
use Game\Model\Poker\Exception AS ExceptionPoker;
use Game\Model\Poker\Exception\ExceptionRule;

class Close extends InputFilter
{
    protected $_game_row;
    
    public function __construct($sm)
    {
        $this->_sm = $sm;
        
        $this->add(array(
            'name'     => 'id',
            'required' => true,
            'validators'=>array(
                array(
                    'name' => 'Digits',
                    'break_chain_on_failure' => true
                ),
                array(
                    'name'  =>  'checkAuth',
                    'break_chain_on_failure' => true
                ),
                array(
                    'name'  =>  'checkFetchRow',
                    'options'   =>  array(
                        'callback'  =>  array($this, 'setGameRow'),
                        'table'     =>  'Poker'
                    ),
                    'break_chain_on_failure' => true
                ),
                array(
                    'name'  =>  'Poker\CheckServer',
                    'break_chain_on_failure' => true
                ),
        )));
        $this->get('id')->setbreakOnFailure(true);
    }
    
    public function setGameRow(GameRow $row)
    {
        $this->_game_row = $row;
        return $this;
    }
    
    public function getGameRow()
    {
        if($this->_game_row === null){
            throw new \Exception('Game not set');
        }
        return $this->_game_row;
    }
    
    
    public function finish()
    {
        $money = 0;
        try{
            $this->getGameRow()->run(function() use(&$money){
                $this->getGameRow()->removeListen($this->getUserRow());
                $money = $this->getGameRow()->removeUser($this->getUserRow());
                $this->getGameRow()->save();
            });
        }catch(\Exception $e) {
            do {
                if($e instanceof ExceptionRule){
                    $this->_messages = array(
                        'poker'  =>  $e->getMessage()
                    );
                    return false;
                }
            } while($e = $e->getPrevious());
        }
        if($money)
            $this->getUserRow()->updateObjectRow('gem', $money);
    }
}