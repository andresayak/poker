<?php

namespace Game\InputFilter\Chat;

use Game\InputFilter\InputFilter;
use Game\Model\Poker\Row AS GameRow;

class Room extends InputFilter
{
    public function __construct($sm)
    {
        $this->_sm = $sm;
        
        $this->add(array(
            'name'     => 'message',
            'required' => true,
            'filters'=>array(
                array('name' => 'StripTags'),
                array('name' => 'StringTrim'),
            ),
            'validators' => array(
                array(
                    'name' => 'not_empty',
                ),
                array(
                    'name' => 'StringLength',
                    'options' => array('max' => 255)
                ),
            )
        ));
        
        $this->add(array(
            'name'     => 'room_id',
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
                array(
                    'name'  =>  'Poker\CheckListen',
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
        $service = $this->getSm()->get('Chat\Service');
        $color = 'default';
        $data = array(
            'type'      =>  'room',
            'room_id'   =>  $this->getValue('room_id'),
            'moderator' =>  ($this->getUserRow()->role == 'moderator'),
            'color'     =>  $color,
            'message'   =>  $this->get('message')->getValue(),
            'time_send' =>  time()
        );
        $this->getUserRow()->addInfo($data);
        $service->addToRoom($data, $this->getValue('room_id'));
    }
}