<?php

namespace Game\InputFilter\Poker;

use Game\InputFilter\InputFilter;

use Game\Model\Poker\Row AS GameRow;
use Game\Model\Poker\Exception\ExceptionRule;

class Bet extends InputFilter
{
    protected $_game_row;
    
    public function __construct($sm)
    {
        $this->_sm = $sm;
        
        $this->add(array(
            'name'     => 'user_id',
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
                        'callback'  =>  array($this, 'setUserRow'),
                        'table'     =>  'User'
                    ),
                    'break_chain_on_failure' => true
                ),
                array(
                    'name'  =>  'Poker\CheckServer',
                    'break_chain_on_failure' => true
                ),
        )));
        $this->get('user_id')->setbreakOnFailure(true);
        
        $this->add(array(
            'name'     => 'id',
            'required' => true,
            'validators'=>array(
                array(
                    'name' => 'Digits',
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
                    'name'  =>  'Poker\CheckStep',
                )
        )));
        
        $this->add(array(
            'name'     => 'value',
            'required' => true,
            'validators'=>array(
                array(
                    'name' => 'Digits',
                    'break_chain_on_failure' => true
                ),
                array(
                    'name'  =>  'Poker\CheckMoney',
                )
        )));
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
        try{
            $this->getGameRow()->raise($this->getUserRow(), $this->getValue('value'));
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
    }
}