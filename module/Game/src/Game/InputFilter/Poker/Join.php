<?php

namespace Game\InputFilter\Poker;

use Game\InputFilter\InputFilter;

use Game\Model\Poker\Row AS GameRow;
use Game\Model\Poker\Exception AS ExceptionPoker;
use Game\Model\Poker\Exception\ExceptionRule;

class Join extends InputFilter
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
            )
        ));
        $this->get('id')->setBreakOnFailure(true);
        $this->add(array(
            'name'     => 'position',
            'required' => true,
            'validators'=>array(
                array(
                    'name' => 'Digits',
                    'break_chain_on_failure' => true
                ),
                array(
                    'name'  =>  'Poker\CheckSeat',
                )
        )));
        $this->get('position')->setBreakOnFailure(true);
        $this->add(array(
            'name'     => 'money',
            'required' => true,
            'validators'=>array(
                array(
                    'name' => 'Digits',
                    'break_chain_on_failure' => true
                ),
                array(
                    'name'  =>  'Ap\Validator\Callback',
                    'options'   =>  array(
                        'callback'  => array($this, 'checkMoney'),
                        'messages'  =>  array('callbackValue'=>'Insufficiently of money')
                    )
                )
        )));
    }
    
    public function checkMoney($value)
    {
        $userObjectRow = $this->getUserRow()->getObjectByCode('chip');
        $userObjectRow->blockForUpdate();
        return ($value <= $userObjectRow->count);
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
            $this->getGameRow()->addUser($this->getUserRow(), $this->get('position')->getValue(), $this->get('money')->getValue());
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
        $this->getUserRow()->updateObjectRow('chip', -$this->get('money')->getValue());
    }
}