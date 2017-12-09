<?php

namespace Game\Model\Poker;

class Card
{
    protected $_use_in_ranking = false;
    protected $_suit, $_value;
    
    public function __construct($suit, $value, $weight) 
    {
        $this->_weight = $weight;
        $this->_suit = $suit;
        $this->_value = $value;
        return $this;
    }

    public function isUsedInRanking()
    {
        return $this->_use_in_ranking;
    }
    
    public function setUseInRanking($bool)
    {
        $this->_use_in_ranking = $bool;
        return $this;
    }
    
    public function weight()
    {
        return $this->_weight;
    }
    
    public function suit()
    {
        return $this->_suit;
    }
    
    public function value()
    {
        return $this->_value;
    }
    
    public function isEqual(Card $card)
    {
        return ($card->value() == $this->value() && $card->suit() == $this->suit());
    }
    
    public function __toString() 
    {
        $suit = $this->suit();
        if(PHP_SAPI == 'cli'){
            if($suit == 'hearts'){
                if(PHP_OS == 'WINNT') {
                    $suit = "\x03";
                } else {
                    $suit = "\xE2\x99\xA5";
                }
            }
            if($suit == 'diamonds'){
                if(PHP_OS == 'WINNT') {
                    $suit = "\x04";
                } else {
                    $suit = "\xE2\x99\xA6";
                }
            }
            if($suit == 'clubs'){
                if(PHP_OS == 'WINNT') {
                    $suit = "\x05";
                } else {
                    $suit = "\xE2\x99\xA3";
                }
            }
            if($suit == 'spades'){
                if(PHP_OS == 'WINNT') {
                    $suit = "\x06";
                } else {
                    $suit = "\xE2\x99\xA0";
                }
            }
        }
        return $this->value().$suit.(($this->_use_in_ranking)?'(R)':'');
    }
    
    public function toArray()
    {
        return array(
            'weight'    =>  $this->_weight,
            'suit'      =>  $this->_suit,
            'value'     =>  $this->_value
        );
    }
}