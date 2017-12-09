<?php

namespace Game\Model\Poker;

class Side
{
    
    protected $_fold = false, $_allin = false;
    public $info, $cards;
    protected $balance = 0, $_play_status = true;

    public function __construct($info) 
    {
        $info['social_name'] = isset($info['social_name'])?$info['social_name']:'Player'.$info['id'];
        $this->info = $info;
        $this->cards = new CardDeck();
    }
    
    public function setBalance($balance)
    {
        $this->balance = $balance;
        return $this;
    }
    
    public function getBalance()
    {
        return $this->balance;
    }
    
    public function updateBalance($balance)
    {
        $this->balance+= $balance;
        return $this;
    }
    
    public function takeCards($cards)
    {
        $this->cards->addCards($cards);
        return $this;
    }
    
    public function setCards(CardDeck $cards)
    {
        $this->cards = $cards;
        return $this;
    }
    
    public function setAllInStatus($status)
    {
        $this->_allin = $status;
        return $this;
    }
    
    public function isAllIn()
    {
        return $this->_allin;
    }
    
    public function setFoldStatus($status)
    {
        $this->_fold = $status;
        return $this;
    }
    
    public function isFold()
    {
        return $this->_fold;
    }
    
    public function setPlayStatus($status)
    {
        $this->_play_status = $status;
        return $this;
    }
    
    public function isPlay()
    {
        return $this->_play_status;
    }
    
    public function toArray()
    {
        return array(
            'id'    =>  (int)$this->info['id'],
            'social_name'    =>  $this->info['social_name'],
            'social_id'  =>  ((isset($this->info['social_id']))?$this->info['social_id']:''),
            'social_link'  =>  ((isset($this->info['social_link']))?$this->info['social_link']:''),
            'money' =>  (int)$this->balance,
            'play'  =>  $this->_play_status,
            'fold'  =>  $this->_fold,
            'allin' =>  $this->_allin,
            'cards' =>  $this->cards->toArray()
        );
    }
    
}