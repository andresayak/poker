<?php

namespace Game\Model\Poker;

class CardDeck
{
    protected $_ranking, $_weight = 0;
    protected $data = array(), $options = array();
    
    public function __construct($options = array()) 
    {
        $this->options = $options;
        return $this;
    }
    
    public function reset() 
    {
        $this->data = array();
        return $this;
    }
    
    public function addCards($cards)
    {
        if($cards instanceof CardDeck){
            foreach ($cards->getCards() AS $card){
                $this->addCard($card);
            }
        }elseif(is_array($cards)){
            foreach ($cards AS $card){
                $this->addCard($card);
            }
        }
        return $this;
    }
    
    public function addCard(Card $card)
    {
        $this->data[] = $card;
        return $this;
    }
    
    public function popCard($count)
    {
        $list = array();
        for($i=0;$i<$count;$i++){
            $list[] = array_pop($this->data);
        }
        return $list;
    }
    
    public function getWinPosition(CardDeck $cards, $weighStatus = false)
    {
        $results = array();
        $maxWeigh = 0;
        foreach($cards->getCards() AS $index=>$card){
            foreach($this->getCards() AS $item){
                $maxWeigh = max($maxWeigh, $card->weight());
                if($item->isEqual($card) && $item->isUsedInRanking()){
                    $results[] = $index+1;
                }
            }
        }
        if($weighStatus && !count($results)){
            foreach($cards->getCards() AS $index=>$card){
                foreach($this->getCards() AS $item){
                    if($item->isEqual($card) && $card->weight() == $maxWeigh){
                        $results[] = $index+1;
                    }
                }
            }
        }
        return $results;
    }
    
    public function copy()
    {
        $array = new CardDeck;
        foreach($this->data AS $card){
            $array->addCard(clone $card);
        }
        return $array;
    }
    
    public function getCards()
    {
        return $this->data;
    }
    
    public function count()
    {
        return count($this->data);
    }
    
    public function getUseCards()
    {
        $data = array();
        foreach($this->data AS $card){
            if($card->isUsedInRanking()){
                $data[] = $card;
            }
        }
        return $data;
    }
    
    public function getMaxWeight($usedInRanking = null)
    {
        $max = 0;
        foreach($this->data AS $card){
            if(!$usedInRanking and !$card->isUsedInRanking() 
                or ($usedInRanking and $card->isUsedInRanking())
                or $usedInRanking === null
             ){
                $max = max($card->weight(), $max);
            }
        }
        return $max;
    }
    
    public function getSecondWeight($usedInRanking = null)
    {
        $primary = $this->getMaxWeight();
        $max = 0;
        foreach($this->data AS $card){
            if((!$usedInRanking and !$card->isUsedInRanking() 
                or ($usedInRanking and $card->isUsedInRanking())
                or $usedInRanking === null
             ) and $card->weight()<$primary)
                $max = max($card->weight(), $max);
        }
        return $max;
    }
    
    public function getWeight()
    {
        return $this->_weight;
    }
    
    public function setWeight($weight)
    {
        $this->_weight = $weight;
        return $this;
    }
    
    public function setRanking($ranking)
    {
        $this->_ranking = $ranking;
        return $this;
    }
    
    public function getRanking()
    {
        return $this->_ranking;
    }
    
    public function toArray()
    {
        $data = array();
        foreach($this->data AS $card){
            $data[] = $card->toArray();
        }
        return $data;
    }
    
    public function __toString()
    {
        return implode(', ',$this->data);
    }
}