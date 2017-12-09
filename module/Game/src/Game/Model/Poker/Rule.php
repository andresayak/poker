<?php

namespace Game\Model\Poker;

use Zend\Log\Logger;

class Rule
{
    protected $logger, $_test_data;
    protected $types = array(
        'suit'  =>  array(
            'clubs', 'diamonds', 'hearts', 'spades'
        ),
        'value' =>  array(
            'ace'=>14, 'king'=>13, 'queen'=>12, 
            'jack'=>11, '10'=>10, '9'=>9, 
            '8'=>8, '7'=>7, '6'=>6, '5'=>5, '4'=>4, '3'=>3, '2'=>2
        )
    );
    protected $ranking = array(
        'royal_flush'   =>  array(
            'suit'  =>  5,
            'sequence'  =>  5,
            'weight'    =>  9,
            'min'   =>  10
        ),
        'straight_flush'   =>  array(
            'suit'  =>  5,
            'sequence'  =>  5,
            'weight'    =>  8
        ),
        'four_kind'   =>  array(
            'equal' =>  4,
            'weight'    =>  7
        ),
        'full_house'   =>  array(
            'equal' =>  array(
                3, 2
            ),
            'weight'    =>  6
        ),
        'flush'   =>  array(
            'suit'  =>  5,
            'weight'    =>  5
        ),
        'straight'   =>  array(
            'sequence'  =>  5,
            'weight'    =>  4
        ),
        'three_kind'   =>  array(
            'equal' =>  3,
            'weight'    =>  3
        ),
        'two_pair'   =>  array(
            'equal' =>  array(
                2, 2
            ),
            'weight'    =>  2
        ),
        'one_pair'   =>  array(
            'equal' =>  2,
            'weight'    =>  1
        ),
        'high_card'   =>  array(
        ),
    );
    
    public function __construct() 
    {
        $this->logger = new Logger;
        $this->logger->addWriter('Null');
    }
    
    public function setLogger(Logger $logger)
    {
        $this->logger = $logger;
        return $this;
    }
    
    public function createCardDesc($items = false)
    {
        $data = [];
        if($items){
            foreach(explode(',', $items) AS $item){
                list($value, $suit) = explode('/',trim($item));
                $data[] = new Card($suit, $value, $this->types['value'][$value]);
            }
        }else{
            foreach($this->types['suit'] AS $suit){
                foreach($this->types['value'] AS $value=>$weight){
                    $data[] = new Card($suit, $value, $weight);
                }
            }
        }
        shuffle($data);
        $cardDesc = new CardDeck();
        $cardDesc->addCards(array_values($data));
        return $cardDesc;
    }
    
    public function setTestWeight($weight)
    {
        $this->_test_data = $weight;
    }
    
    public function calcWin($players, $publicCards, $banks, $dealerPosition)
    {
        $hands = array();
        $weights = array();
        
        foreach ($players AS $position => $player) {
            if (!$player->isPlay()) {
                $this->logger->info('player  = ' . $player->info['username'] . ' not play');
                continue;
            }
            if ($player->isFold()) {
                $this->logger->info('player  = ' . $player->info['username'] . ' is fold');
                continue;
            }
            $cards = $this->calcWeight($player->cards, $publicCards);
            $hands[$position] = $cards;
            $this->logger->info('cards: '. $cards);
            $this->logger->info('player  = ' . $player->info['username'] . ' (' . $position . ') cards [' .  $player->cards . '] use [' . implode(', ', $cards->getUseCards()) . ']'
                . ' ranking [' . $cards->getRanking() . ']'
                . ' weight ' . $cards->getWeight());
            $weights[$position] = $cards->getWeight();
        }
        if($this->_test_data!==null){
            $weights = $this->_test_data['weights'];
        }
        $win = array();
        foreach($banks AS $index=>$bank){
            $win[$index] = array(
                'bank'      =>  $bank['money'],
                'positions' =>  array()
            );
            $weightPlayInBank = array();
            foreach($bank['positions'] AS $position){
                if(isset($weights[$position])){
                    $weightPlayInBank[$position] = $weights[$position];
                }
            }
            if(!count($weightPlayInBank)){
                $this->logger->err('invalid count'."\n".'weights= '.print_r($weights, true).' banks= '.print_r($bank, true));
            }
            $keys = array_keys($weightPlayInBank);
            $maxWeight = (count($weightPlayInBank)==1)?$weightPlayInBank[$keys[0]]:max($weightPlayInBank);
            $countValues = array_count_values($weightPlayInBank);
            $winCount = $countValues[$maxWeight];
            $winPositions = array();
            $indexPos = false;
            foreach($bank['positions'] AS $position){
                if(isset($weightPlayInBank[$position]) and $maxWeight == $weightPlayInBank[$position]){
                    $winPositions[] = $position;
                }
            }
            sort($winPositions);
            foreach($winPositions AS $position){
                if($position > $dealerPosition){
                    $indexPos = $position;
                }
            }
            if($indexPos === false){
                $indexPos = $winPositions[0];
            }
            foreach($winPositions AS $position){
                $player = $players[$position];
                if ($indexPos == $position) {
                    $money = ceil($bank['money'] / $winCount);
                } else {
                    $money = floor($bank['money'] / $winCount);
                }
                $win[$index]['positions'][$position] = array(
                    'user' => $player->toArray(),
                    'money' => $money,
                    'money_real' => $bank['money'] / $winCount,
                    'ranking' => $hands[$position]->getRanking(),
                    'weight' => $hands[$position]->getWeight(),
                    'public' => $hands[$position]->getWinPosition($publicCards),
                    'private' => $hands[$position]->getWinPosition($player->cards, true),
                );
            }
            if($win[$index]['positions'] == 0){
                throw new \Exception('invalid count win count');
            }
        }
        return $win;
    }
    
    public function calcWeight($playerCards, $publicCards)
    {
        
        $cards = new CardDeck();
        $cards->addCards($publicCards->copy())
            ->addCards($playerCards->copy());
        $c = count($this->types['value']);
        $weightRanking = $this->weightRanking($cards)*pow($c,4);
        $weightMaxRanking = $cards->getMaxWeight(true)*pow($c,3);
        
        $cardMaxWeight = $playerCards->getMaxWeight()*pow($c,2);
        $cardSecondWeight = $playerCards->getSecondWeight()*pow($c,1);
        $weight = $weightRanking + $cardMaxWeight + $weightMaxRanking + $cardSecondWeight;
        //echo '$weight = '.$weight."\n";
        //echo '-$weightRanking = '.$weightRanking."\n";
        //echo '-$cardMaxWeight = '.$cardMaxWeight."\n";
        //echo '-$weightMaxRanking = '.$weightMaxRanking."\n";
        //echo '-$cardSecondWeight = '.$cardSecondWeight."\n";
        $cards->setWeight($weight);
        return $cards;
    }
    
    public function weightRanking(CardDeck $cards)
    {
        foreach($this->ranking AS $name=>$options){
            $status = null;
            if(isset($options['equal']) and $status!==false){
                $status = $this->isEqual($cards, $options['equal']);
            }
            if(isset($options['suit']) and $status!==false){
                $status = $this->isSuit($cards, $options['suit']);
            }
            if(isset($options['sequence']) and $status!==false){
                $status = $this->isSequence($cards, $options['sequence'], isset($options['min'])?$options['min']:false);
            }
            if($status === true){
                $cards->setRanking($name);
                return $options['weight'];
            }
        }
        $cards->setRanking('high_card');
        return 0;
    }
    
    public function isSequence($cards, $sequenceNeed, $min)
    {
        $values = array();
        foreach($cards->getCards() AS $card){
            $values[] = $card->weight();
        }
        arsort($values);
        $values = array_unique($values);
        $prev = null;
        $first = null;
        $sequence = 1;
        $weights = array();
        foreach($values AS $value){
            if($prev === null){
                $first = $value;
                $prev = $value;
                $weights[] = $value;
                continue;
            }elseif($prev-1 == $value){
                $sequence++;
                $weights[] = $value;
            }else{
                $sequence = 1;
                $weights = array();
                $weights[] = $value;
            }
            $prev = $value;
            if(($sequence+1 == $sequenceNeed and $value == 2 and $first == 14)){
                $sequence++;
                $weights[] = $value;
            }
            if($sequence >= $sequenceNeed 
                and (
                   !$min or ($min == $value)
                )
            ){
                foreach($weights AS $weight){
                    foreach($cards->getCards() AS $card){
                        if($weight == $card->weight()){
                            $card->setUseInRanking(true);
                        }
                    }
                }
                return true;
            }
        }
        return false;
    }
    
    public function isEqual($cards, $equalNeeds)
    {
        $equalHave = array();
        foreach($cards->getCards() AS $card){
            $value = $card->value();
            if(!isset($equalHave[$value])){
                $equalHave[$value] = 0;
            }
            $equalHave[$value]++;
        }
                
        $gets = array();
        if(!is_array($equalNeeds)){
            $equalNeeds = array($equalNeeds);
        }
        arsort($equalNeeds);
        arsort($equalHave);
        $limits = array();
        foreach ($equalNeeds AS $equalNeed) {
            $status = false;
            foreach ($equalHave as $value => $count) {
                if ($count >= $equalNeed and !in_array($value, $gets)) {
                    $gets[] = $value;
                    $limits[$value] = $equalNeed;
                    $status = true;
                    break;
                }
            }
            if (!$status) {
                return false;
            }
        }
        $limit = 0;
        foreach ($cards->getCards() AS $card) {
            if (in_array($card->value(), $gets)) {
                $card->setUseInRanking(true);
                if (++$limit == $limits[$card->value()]) {
                    //break;
                }
            }
        }
        return true;
    }
    
    public function isSuit($cards, $suitNeed)
    {
        $suitHave = array();
        foreach($cards->getCards() AS $card){
            $suit = $card->suit();
            if(!isset($suitHave[$suit])){
                $suitHave[$suit] = 0;
            }
            $suitHave[$suit]++;
        }
        arsort($suitHave);
        foreach($suitHave AS $suit=>$count){
            if($count >= $suitNeed){
                $limit = 0;
                foreach($cards->getCards() AS $card){
                    if($card->suit() == $suit){
                        $card->setUseInRanking(true);
                        if(++$limit == $suitNeed){
                            break;
                        }
                    }
                }
                return true;
            }
        }
        
        return false;
    }
}