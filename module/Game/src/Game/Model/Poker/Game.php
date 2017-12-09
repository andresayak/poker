<?php

namespace Game\Model\Poker;

use Zend\Log\Logger;
use Game\Model\Poker\Row AS GameRow;
use Game\Model\User\Row AS UserRow;

class Game
{
    protected $options = array(
        'manual_mode'       =>  false,
        'min_player'        =>  2,
        'max_player'        =>  10,
        'time'              =>  30,
        'rounds'            =>  3,
        'hand_cards'        =>  2,
        'public_cards'      =>  3,
        'public_cards_max'  =>  5,
        'blinds'            =>  array(5,10),
    );
    public $says = array();
    public $isStepEnd = true;
    public $isGameEnd = true;
    public $dealerPosition, $turnPosition;
    protected $logger;
    protected $positions;
    public $cardDesc, $bank;
    public $step, $rule, $_listener;
    protected $_game_row;
    
    public function __construct($options = array())
    {
        $this->setOptions($options);
        $this->clear();
    }

    public function getOptions()
    {
        return $this->options;
    }
    
    public function setOptions($options = array())
    {
        $this->options = array_merge($this->options, $options);
        return $this;
    }
    
    public function setListener($listener)
    {
        $this->_listener = $listener;
        return $this;
    }
    
    public function setPublicCards(CardDeck $cards)
    {
        $this->publicCards = $cards;
        return $this;
    }
    
    public function setGameRow(GameRow $gameRow)
    {
        $this->_game_row = $gameRow;
        $cacheData = $gameRow->getCacheData();
        if($cacheData!== null and $cacheData){
            foreach($cacheData AS $var=>$value){
                $this->{$var} = $value;
            }
            $this->rule->setLogger($this->logger);
            $this->bank->setLogger($this->logger);
        }
        $seats = $gameRow->getSeats();
        $clearPositions = array();
        foreach($this->positions AS $position=>$player){
            if(!isset($seats[$position])){
                $clearPositions[] = $position;
            }
        }
        
        foreach($clearPositions AS $pos){
            unset($this->positions[$pos]);
        }
        
        return $this;
    }
    
    public function clear()
    {
        $this->step = 0;
        $this->dealerPosition = null;
        $this->turnPosition = null;
        $this->isStepEnd = true;
        $this->isGameEnd = true;
        $this->cardDesc = new CardDeck();
        $this->bank = new Bank();
        $this->rule = new Rule();
        $this->positions = new \ArrayObject;
        $this->cardDesc = $this->rule->createCardDesc();
        $this->publicCards = new CardDeck();
        $this->logger = new Logger;
        $this->logger->addWriter('Null');
    }
    
    public function getCacheData()
    {
        return array(
            'says'      =>  $this->says,
            'cardDesc'  =>  $this->cardDesc,
            'publicCards'   =>  $this->publicCards,
            'bank'  =>  $this->bank,
            'dealerPosition' => $this->dealerPosition,
            'turnPosition' => $this->turnPosition,
            'isStepEnd' => $this->isStepEnd,
            'isGameEnd' => $this->isGameEnd,
            'step' => $this->step,
            'positions' =>  $this->positions
        );
    }
    
    public function toArrayForApi($userRow = false)
    {
        $data = array(
            'says'  =>  $this->says,
            'time'  =>  $this->options['time'],
            'publicCards'   =>  $this->publicCards->toArray(),
            'bank'  =>  $this->bank->toArrayForApi(),
            'dealerPosition' => $this->dealerPosition,
            'turnPosition' => $this->turnPosition,
            'isStepEnd' => $this->isStepEnd,
            'isGameEnd' => $this->isGameEnd,
            'step' => $this->step,
            'positions' =>  array()
        );
        foreach($this->positions AS $position=>$side){
            $data['positions'][$position] = $side->toArray();
            if($userRow && $side->info['id'] != $userRow->id){
                $data['positions'][$position]['cards'] = array(array(), array());
            }
        }
        return $data;
    }
    
    public function getGameRow()
    {
        return $this->_game_row;
    }
    
    public function getOption($name)
    {
        return $this->options[$name];
    }
    
    public function setLogger(Logger $logger)
    {
        $this->logger = $logger;
        $this->rule->setLogger($logger);
        $this->bank->setLogger($logger);
        return $this;
    }
    
    public function getLogger()
    {
        return $this->logger;
    }
    
    public function createPlayer($info, $balance)
    {
        $side = new Side($info);
        $side->setBalance($balance);
        return $side;
    }
    
    public function getPlayers()
    {
        return $this->positions;
    }
    
    public function join(Side $player, $position)
    {
        if(isset($this->positions[$position])){
            $this->logger->err('join: '.$position.' = position is busy');
            throw new Exception\ExceptionRule('position is busy');
        }
        if($this->_getPostionByPlayer($player)!==false){
            $this->logger->err('join: '.$position.' = player in game');
            throw new Exception\ExceptionRule('player in game');
        }
        
        $this->logger->info('join player = '. $player->info['username']);
        $this->positions[$position] = $player;
        $this->positions->ksort();
        $this->send('join', array(
            'position' =>  $position,
            'user'  =>  $player->toArray()
        ), 'all');
        $this->ifCanStart();
        return $this;
    }
    
    public function leave(Side $player, $type = 'timeout', $money = 0)
    {
        if($player->isAllIn()){
            throw new Exception\ExceptionRule('player in allin');
        }
        $pos = $this->_getPostionByPlayer($player);
        $nextTurnPosition = $this->_getNextTurnPosition();
        if($pos!== false){
            $this->logger->info('leave player = '. $player->info['username'].' (type='.$type.')');
            if(isset($this->says[$pos]))
                unset($this->says[$pos]);
            
            unset($this->positions[$pos]);
            $this->send('leave', array(
                'leavetype'  =>  $type,
                'position'  =>  $pos,
                'money'     =>  $money,
                'user'      =>  $player->toArray()
            ), 'all');
            if($type == 'close'){
                $this->turnNext($nextTurnPosition);
            }
        }
        return $this;
    }
    
    public function clearPlayers()
    {
        $clearPositions = array();
        foreach($this->positions AS $pos=>$player){
            $player->setFoldStatus(false)
                ->setAllInStatus(false);
            $player->cards->reset();
            if($player->getBalance() < $this->options['blinds'][1]){
                $clearPositions[] = $pos;
                $this->logger->info('kick '.$player->info['username']);
            }
        }
        foreach($clearPositions AS $pos){
            $player = $this->positions[$pos];
            if($this->_game_row instanceof GameRow){
                $this->_game_row->removeUserByPlayer($player, 'no_money');
            }else{
                $this->leave($player, 'no_money');
            }
        }
        return $this;
    }
    
    public function start()
    {
        if(!$this->isGameEnd){
            throw new Exception\ExceptionRule('game is active');
            return $this;
        }
        if(!$this->ifHavePlayers()){
            throw new Exception\ExceptionRule('not have normal count players');
        }
        if($this->_game_row instanceof GameRow){
            $this->_game_row->calcTimeOut();
        }
        $this->isGameEnd = false;
        $this->logger->info('start game ['. count($this->positions).' players]');
        $this->step = 0;
        if($this->_game_row instanceof GameRow){
            $this->_game_row->status = 1;
        }
        $this->nextDealer();
        $this->step();
        return $this;
    }
    
    public function end()
    {
        if($this->isGameEnd){
            throw new Exception\ExceptionRule('game not active');
        }
        $this->isGameEnd = true;
        $this->logger->info('GameEnd');
        if($this->_game_row instanceof GameRow){
            $this->_game_row->status = 0;
            $this->_game_row->time_start = null;
        }
        $cards = [];
        $wins = $this->calcWins($cards);
        if(count($wins) == 0){
            //todo прорахувати виконання при $wins = array()
        }
        $bankSum = 0;
        foreach($wins AS $win){
            $bankSum+= $win['bank'];
        }
        $balances = array();
        foreach($this->positions AS $position=>$player){
            $balances[$position] = $player->getBalance();
            $this->logger->info('balance '.$player->info['username'].' = '.$player->getBalance()); 
        }
        $this->logger->info('callSum = '.$this->getCallSum());
        $this->logger->info('bankSum = '.$bankSum);
        $this->send('win', array(
            'balances'  =>  $balances,
            'cards' =>  $cards,
            'wins'  =>  $wins,
        ), 'all');
        $this->clearPlayers();
        if($this->options['manual_mode']){
            return $this;
        }
        $this->bank->reset();
        $this->ifCanStart(10 + (count($wins)-1)*6);
        return $this;
    }
    
    public function setWins($wins){
        $this->_wins = $wins;
    }
    
    public function calcWins(&$cards = array())
    {
        $banks = $this->getBanks();
        $wins = $this->rule->calcWin($this->positions, $this->publicCards, $banks, $this->dealerPosition);
        $bankSum = 0;
        foreach($banks AS $bank){
            $bankSum+=$bank['money'];
        }
        $getMoney = 0;
        foreach($wins AS $win){
            foreach($this->positions AS $position=>$player){
                if($player->isPlay() && !$player->isFold()){
                    $cards[$position] = $player->cards->toArray();
                    if(isset($win['positions'][$position])){
                        $money = $win['positions'][$position]['money'];
                        $this->updateBalance($position, $money);
                        $this->logger->info('win '.$player->info['username'].' = '.$money);
                        $getMoney+=$money;
                    }
                }
            }
        }
        if($getMoney!=$bankSum){
            throw new Exception\ExceptionRule('not all bank win (bank = '.$bankSum.', get='.$getMoney.')');
        }
        return $wins;
    }
    
    public function ifHavePlayers()
    {
        return (count($this->positions)>=$this->options['min_player'] 
            && count($this->positions)<=$this->options['max_player']);
    }
    
    public function ifCanStart($timeDiff = 5)
    {
        if($this->options['manual_mode']){
            return $this;
        }
        if($this->isGameEnd and $this->ifHavePlayers()){
            if($this->_game_row instanceof GameRow){
                $this->_game_row->time_start = time() + $timeDiff;
            }else{
                $this->logger->info('ifCanStart = true');
                $this->start();
            }
        }
        return $this;
    }
    
    public function ifCanEnd()
    {
        $count = 0;
        foreach($this->positions AS $player){
            if(!$player->isFold()
                and $player->isPlay()
            ){
                $count++;
            }
        }
        if($count<=1){
            $this->logger->info('ifCanEnd = true / '.(int)$this->isGameEnd);
            $this->end();
            return true;
        }
        return false;
    }
    
    public function nextDealer()
    {
        $keys = array_keys($this->positions->getArrayCopy());
        if($this->dealerPosition === null){
            $this->dealerPosition = $keys[0];
        }else{
            $index = array_search($this->dealerPosition, $keys);
            $this->dealerPosition = $keys[($index+1)%count($this->positions)];
        }
        
        $this->logger->info('dealerPosition = '.$this->dealerPosition);
        $this->cardDesc = $this->rule->createCardDesc();
        $this->bank->reset();
        $this->publicCards->reset();
        foreach($this->positions AS $pos=>$player){
            $player->setPlayStatus(true);
        }
        return $this;
    }

    public function send($name, $params, $allOrUserId = 'all')
    {
        if($this->_listener!== null 
            and $this->_listener instanceof \Closure
        ){
            $params['type'] = 'poker';
            $params['name'] = $name;
            $params['step'] = $this->step;
            if($this->_game_row instanceof GameRow){
                $params['room_id'] = (int)$this->getGameRow()->id;
                $params['time_update'] = (int)$this->getGameRow()->time_update;
            }
            $params['dealerPosition'] = $this->dealerPosition;
            $f = $this->_listener;
            $f($params, $allOrUserId);
        }
        return $this;
    }
    
    public function step()
    {
        $this->isStepEnd = false;
        $this->clearSays();
        $this->logger->info('step '.$this->step);
        if($this->step == 0){
            $this->_blindes();
            foreach($this->positions AS $player){
                $cards = $this->cardDesc->popCard($this->options['hand_cards']);
                if(!$cards){
                    $this->logger->err('not have cards');
                    throw Exception\ExceptionRule('not have cards');
                }
                $this->logger->info('player  = '.$player->info['id'].' take: '.  implode(', ', $cards));
                $player->takeCards($cards);
                $this->send('privateCards', array(
                    'cards' => $player->cards->toArray(),
                    'player_id' => $player->info['id']
                    ), $player->info['id']);
            }
            if($this->isAllinEnd()){
                $this->stepEnd();
            }
            return $this;
        }else if($this->step == 1){
            $cards = $this->cardDesc->popCard($this->options['public_cards']);
            $this->publicCards->addCards($cards);
        }else if($this->step == 2){
            $cards = $this->cardDesc->popCard(1);
            $this->publicCards->addCards($cards);
        }else if($this->step == 3){
            $cards = $this->cardDesc->popCard(1);
            $this->publicCards->addCards($cards);
        }
        $this->send('publicCards', array(
            'cards'=>$this->publicCards->toArray()
        ), 'all');
        $this->logger->info('desc cards: '.  implode(', ', $this->publicCards->getCards()));
        $this->turnNext();
        /*if($this->isAllinEnd()){
            $this->stepEnd();
        }*/
        return $this;
    }
    
    protected function _blindes()
    {
        $users = array();
        foreach($this->positions AS $position=>$player){
            $users[$position] = $player->toArray();
            $this->logger->info('balance '.$player->info['username'].' = '.$player->getBalance().' (pos = '.$position.')'); 
        }
        $logs = array(
            'users'     =>  $users,
            'blindes'   =>  array()
        );
        $this->logger->info('blindes  = '.implode('/',$this->options['blinds']));
        foreach($this->options['blinds'] AS $index=>$blind){
            $position = $this->_blinde($index+1, $blind, $logs);
        }
        $this->turnPosition = $position;
        $this->send('start', $logs, 'all');
        $this->turnNext();
        //$this->logger->info('turnPosition  = '.$position);
    }
    
    protected function _blinde($position, $value, &$logs)
    {
        $keys = array_keys($this->positions->getArrayCopy());
        $index = array_search($this->dealerPosition, $keys);
        $index = $keys[($index+$position)%count($this->positions)];
        $this->updateBalance($index, -$value);
        $this->logger->info('player  = '.$this->positions[$index]->info['username'].' get blind: '.  $value);
        $this->bank->add($value, $this->positions[$index], $this->step);
        $logs['blindes'][] = array(
            'position'  =>  (int)$index,
            'user_id'   =>  (int)$this->positions[$index]->info['id'],
            'value'     =>  (int)$value,
        );
        return $index;
    }
    
    public function timeout($player)
    {
        if($this->isGameEnd){
            throw new Exception\ExceptionRule('game not active');
        }
        if($player->isAllIn()){
            throw new Exception\ExceptionRule('player in allin');
        }
        $position = $this->_getPostionByPlayer($player);
        if(!$position){
            $this->logger->err('timeout: player not have position');
            throw new Exception\ExceptionRule('player not have position');
        }
        $nextTurnPosition = $this->_getNextTurnPosition();
        $result = array();
        if($this->_game_row instanceof GameRow){
            if($this->_game_row->time_update > time()){
                return false;
            }
            $result = $this->_game_row->removeUserByPlayer($player);
        }else{
            $this->leave($player);
        }
        $this->send('timeout', array(
            'position'  =>  (int)$this->turnPosition,
            'user'      =>  $player->toArray()
        ), 'all');
        $this->says[$position] = true;
        $this->turnNext($nextTurnPosition);
        $this->logger->info('player  = '.$player->info['username'].' timeout');
        
        return $result;
    }
    
    public function check(Side $player)
    {
        if($this->isGameEnd){
            throw new Exception\ExceptionRule('game not active');
        }
            
        $position = $this->_getPostionByPlayer($player);
        if($position!== false){
            if(!$player->isPlay()){
                $this->logger->err('check: you not play');
                throw new Exception\ExceptionRule('you not play');
            }
            if($this->turnPosition != $position){
                $this->logger->err('check: not your step, turnPosition = '.$this->turnPosition);
                throw new Exception\ExceptionRule('Not your step, turnPosition = '.$this->turnPosition);
            }
            if($this->bank->getCallValue($player->info['id'], $this->step)){
                $this->logger->err('check: need use call');
                throw new Exception\ExceptionRule('need use call');
            }
            $this->says[$position] = true;
            $this->addToBank(0, $player, 'check');
        }
        return $this;
    }
    
    public function call(Side $player)
    {
        if($this->isGameEnd){
            throw new Exception\ExceptionRule('game not active');
        }
        $type = 'call';
        $position = $this->_getPostionByPlayer($player);
        if($position!== false){
            if(!$player->isPlay()){
                $this->logger->err('call: you not play');
                throw new Exception\ExceptionRule('you not play');
            }
            if($this->turnPosition != $position){
                $this->logger->err('call: not your step, turnPosition = '.$this->turnPosition);
                throw new Exception\ExceptionRule('Not your step, turnPosition = '.$this->turnPosition);
            }
            $callValue = $this->bank->getCallValue($player->info['id'], $this->step);
            if($player->getBalance() <= $callValue){
                $callValue = $player->getBalance();
                $player->setAllInStatus(true);
                $type = 'allin';
            }
            if(!$callValue){
                $this->logger->err('call: need use check');
                throw new Exception\ExceptionRule('need use check');
            }
            $this->says[$position] = true;
            $this->updateBalance($position, -$callValue);
            $this->addToBank($callValue, $player, $type, false);
        }
        return $this;
    }
    
    public function updateBalance($position, $value)
    {
        if($this->_game_row instanceof GameRow){
            $this->_game_row->updateBalance($position, $value);
        }
        $this->positions[$position]->updateBalance($value);
    }
    
    public function addToBank($value, $player, $type, $clearSays=false)
    {
        if($type == 'check' && $value){
            $this->logger->err('invalid check');
            throw new Exception\ExceptionRule('invalid check');
        }
        if($type != 'check' && !$value){
            $this->logger->err('invalid check');
            throw new Exception\ExceptionRule('invalid check');
        }
        $this->bank->add($value, $player, $this->step, ($type == 'allin'));
        $allValue = $this->bank->getCallSum($player->info['id'], $this->step);
        $position = $this->_getPostionByPlayer($player);
        $this->send($type, array(
            'position'  =>  (int)$position,
            'value'     =>  (int)$value,
            'all'       =>  (int)$allValue,
            'user'      =>  $player->toArray()
        ), 'all');
        $this->logger->info('=========== '.$player->info['username'].' '.$type.': '.$allValue);
        
        if($clearSays){
            $this->clearSays();
        }
        $this->says[$position] = true;
            
        $this->turnNext();
    }
    
    public function fold(Side $player)
    {
        if($this->isGameEnd){
            throw new Exception\ExceptionRule('game not active');
        }
        $position = $this->_getPostionByPlayer($player);
        if($position!== false){
            if($player->isFold()){
                $this->logger->err('fold: you already is fold');
                throw new Exception\ExceptionRule('you already is fold');
            }
            if(!$player->isPlay()){
                $this->logger->err('fold: you not play');
                throw new Exception\ExceptionRule('you not play');
            }
            if($player->isAllIn()){
                throw new Exception\ExceptionRule('player in allin');
            }
            if($this->turnPosition != $position){
                $this->logger->err('raise: not your step, turnPosition = '.$this->turnPosition);
                throw new Exception\ExceptionRule('Not your step, turnPosition = '.$this->turnPosition);
            }
            $player->setFoldStatus(true);
            $this->send('fold', array(
                'position'  =>  (int)$position,
                'user'      =>  $player->toArray()
            ), 'all');
            unset($this->says[$position]);
            $this->logger->info('player  = '.$player->info['username'].' fold');
            if($this->turnPosition == $position){
                $this->turnNext();
            }else{
                $this->ifCanEnd();
            }
        }
        return $this;
    }
    
    public function getMaxRaise($player)
    {
        $raiseMax = 0;
        foreach ($this->positions AS $item) {
            if ($item->info['id'] != $player->info['id'] 
                && $item->isPlay() && !$item->isAllIn() && !$item->isFold()
            ) {
                $ItemValueSum = $this->bank->getCallSum($item->info['id'], $this->step);
                $raiseMax = max($raiseMax, $item->getBalance() + $ItemValueSum);
            }
        }
        return $raiseMax;
    }
    
    
    public function getMinRaise()
    {
        $raiseMin = 0;
        foreach ($this->positions AS $item) {
            if ($item->isPlay() && !$item->isAllIn()  && !$item->isFold()) {
                $ItemValueSum = $this->bank->getCallSum($item->info['id'], $this->step);
                $raiseMin = max($raiseMin, $ItemValueSum);
            }
        }
        return $raiseMin;
    }
    
    public function raise(Side $player, $raiseValue, $type = 'raise')
    {
        if($this->isGameEnd){
            throw new Exception\ExceptionRule('game not active');
        }
        $position = $this->_getPostionByPlayer($player);
        if($position!== false){
            if(!$player->isPlay()){
                $this->logger->err('raise: you not play');
                throw new Exception\ExceptionRule('you not play');
            }
            if($player->isFold()){
                $this->logger->err('fold: player is fold');
                throw new Exception\ExceptionRule('player is fold');
            }
            if($player->isAllIn()){
                throw new Exception\ExceptionRule('player in allin');
            }
            if($this->turnPosition != $position){
                $this->logger->err('raise: not your step, turnPosition = '.$this->turnPosition);
                throw new Exception\ExceptionRule('Not your step, turnPosition = '.$this->turnPosition);
            }
            $callValue = $this->bank->getCallValue($player->info['id'], $this->step);
            $valueSum = $this->bank->getCallSum($player->info['id'], $this->step);
            $needValue = $raiseValue - $valueSum;
            $raiseMin = $this->getMinRaise();
            if($valueSum >= $raiseValue){
                throw new Exception\ExceptionRule('нельзя делать рейс меньше ставки на столе');
            }
            if($player->getBalance() < $needValue){
                $this->logger->err('raise: no money (balance='.$player->getBalance().', value='.$needValue.')');
                throw new Exception\ExceptionRule('no money');
            }
            if($needValue == $player->getBalance()){
                throw new Exception\ExceptionRule('need use allin');
            }
            if($this->_getCountNotAllIn() == 1){
                $this->logger->err('raise: all in allin');
                throw new Exception\ExceptionRule('нельзя делать рейс еcли все на allin');
            }
            
            $raiseMax = $this->getMaxRaise($player);
            
            if($raiseValue>$raiseMax){
                throw new Exception\ExceptionRule('big raise');
            }
            if($raiseValue <= $callValue and !$player->isAllIn()){
                $this->logger->err('raise('.$type.'):['.$player->info['id'].'] RaiseValuse < callValue = ['.$raiseValue.' <=  '.$callValue.']');
                throw new Exception\ExceptionRule('RaiseValuse < callValue');
            }
            $this->updateBalance($position, -$needValue);
            $this->addToBank($needValue, $player, $type, true);
        }
        return $this;
    }
    
    public function isAllSay()
    {
        if(count($this->says)==1){
            foreach($this->says AS $position=>$say){
                $player = $this->positions[$position];
                $callValue = $this->bank->getCallValue($player->info['id'], $this->step);
                if($callValue == 0){
                    return true;
                }
            }
        }
        foreach($this->says AS $position=>$say){
            if($say!==true){
                return false;
            }
        }
        return true;
    }
    
    public function isAllinEnd()
    {
        $count = 0;
        
        foreach($this->positions AS $position=>$player){
            if($player->isPlay()
                && !$player->isFold()
                && !$player->isAllIn()
            ){
                $count++;
            }
        }
        return ($this->_getCountNotAllIn() == 1);
    }
    
    protected function _getCountNotAllIn()
    {
        $count = 0;
        foreach($this->positions AS $position=>$player){
            if($player->isPlay()
                && !$player->isFold()
                && !$player->isAllIn()
            ){
                $count++;
            }
        }
        return $count;
    }
    
    public function clearSays()
    {
        $count = $this->_getCountNotAllIn();
        $this->says = array();
        //$this->logger->info('clearSays count = '.$count);
        foreach($this->positions AS $position=>$player){
            if(!$player->isPlay()){
                continue;
            }
            $callValue = $this->bank->getCallValue($player->info['id'], $this->step);
            if($player->isFold() || $player->isAllIn() || ($callValue == 0 and $count == 1)){
                //$this->says[$position] = true;
            }else{
                $this->says[$position] = false;
            }
            //$this->logger->info(' - '.$player->info['username'].' = '.($this->says[$position]?'Y':'N').' = '.(($this->says[$position])?'Yes':'No').' fold = '.($player->isFold()?'Y':'N')
            //        .' play = '.($player->isPlay()?'Y':'N')
            //       .' allIn = '.($player->isAllIn()?'Y':'N').' calValue = '.$this->bank->getCallValue($player->info['id'], $this->step));
        }
        return $this;
    }
    
    public function reraise(Side $player, $value)
    {
        return $this->raise($player, $value, 'reraise');
    }
    
    public function allin(Side $player)
    {
        if($player->isAllIn()){
            $this->logger->err('allin: you already is allin');
            throw new Exception\ExceptionRule('you already is allin');
        }
        $position = $this->_getPostionByPlayer($player);
        if($position!== false){
            if(!$player->isPlay()){
                $this->logger->err('raise: you not play');
                throw new Exception\ExceptionRule('you not play');
            }
            if($player->isFold()){
                $this->logger->err('fold: player is fold');
                throw new Exception\ExceptionRule('player is fold');
            }
            if($this->turnPosition != $position){
                $this->logger->err('allin: not your step, turnPosition = '.$this->turnPosition);
                throw new Exception\ExceptionRule('Not your step, turnPosition = '.$this->turnPosition);
            }
            $valueSum = $this->bank->getCallAllSum($player->info['id']);
            $needSum = $this->bank->getCallValue($player->info['id'], $this->step);
            $maxBalance = 0 ;
            $count = 0;
            foreach($this->positions AS $item){
                if($item->info['id'] == $player->info['id']){
                    continue;
                }
                if($item->isPlay() && !$item->isFold() && !$item->isAllIn()){
                    $callAll = $this->bank->getCallAllSum($item->info['id']);
                    $maxBalance = max($maxBalance, $item->getBalance());
                    $count++;
                }
            }
            if($maxBalance == 0){
                $allinValue = $this->bank->getCallValue($player->info['id'], $this->step);
            }else{
                $allinValue = min($maxBalance, $player->getBalance()-$needSum)+$needSum;
            }
            $player->setAllInStatus(true);
            $this->updateBalance($position, -$allinValue);
            $this->addToBank($allinValue, $player, 'allin', true);
        }
        return $this;
    }
    
    public function getPlayerByUser(UserRow $userRow)
    {
        foreach($this->positions AS $item){
            if($item->info['id'] == $userRow->id){
                return $item;
            }
        }
        return false;
    }
    
    protected function _getPostionByPlayer(Side $player)
    {
        foreach($this->positions AS $index=>$item){
            if($item->info['id'] == $player->info['id']){
                return $index;
            }
        }
        return false;
    }
    
    public function getTurnPlayer()
    {
        if(!isset($this->positions[$this->turnPosition])){
            return false;
        }
        return $this->positions[$this->turnPosition];
    }
    
    public function stepEnd()
    {
        $this->logger->info(' - isAllinEnd ' . ($this->isAllinEnd()?'true':'false'));
        $this->logger->info(' - isCompared ' . ($this->bank->isCompared($this->positions->getArrayCopy(), $this->step) ?'true':'false'));
        $this->logger->info(' - isAllSay ' . ($this->isAllSay()?'true':'false'));
        if(
            (($this->isAllinEnd() or
            $this->bank->isCompared($this->positions->getArrayCopy(), $this->step))
            && $this->isAllSay())
        ){
            $this->isStepEnd = true;
            $this->logger->info('step ' . $this->step . ' end / Bank = ' . $this->bank->getBalance());
            $this->send('stepEnd', array(
                'step' => $this->step,
                'bank' => $this->bank->getBalance()
            ), 'all');
            if ($this->step == $this->options['rounds']) {
                $this->end();
                return true;
            } else {
                $this->step++;
                $this->step();
                return true;
            }
            
        }
        return false;
    }
    
    public function _getNextTurnPosition()
    {
        $keys = array_keys($this->positions->getArrayCopy());
        $index = array_search($this->turnPosition, $keys);
        $count = count($keys);
        return $keys[($index+1)%$count];
    }
    
    public function turnNext($turnPosition = false)
    {
        if($this->isGameEnd or $this->ifCanEnd()){
            return $this;
        }
        if($this->stepEnd()){
            return $this;
        }
        if($turnPosition){
            $this->turnPosition = $turnPosition;
        }else{
            $this->turnPosition = $this->_getNextTurnPosition();
        }
        $this->logger->info('turnNext position = '.$this->turnPosition);
        
        if ($this->getTurnPlayer()->isFold()
            or $this->getTurnPlayer()->isAllIn()
            or ! $this->getTurnPlayer()->isPlay()
        ) {
            $this->logger->info(' - turnNext');
            return $this->turnNext();
        }else{
            
            if($this->_game_row instanceof GameRow){
                $this->_game_row->calcTimeOut();
            }
            $this->send('turnNext', array(
                'turnPosition'=> $this->turnPosition
            ), 'all');
        }
    }
    
    public function getBanks()
    {
        return $this->bank->getBanks($this->positions);
    }
    
    public function status()
    {
        if(!$this->isGameEnd){
            $call = $this->bank->getCallStep($this->step);
            $str = 'Public cards: '.$this->publicCards."\n"
                .' - call: '.$call."\n"
                .' - step: '.$this->step."\n"
                .' - bank: '.$this->bank->getBalance()."\n";
            foreach($this->positions AS $position=>$player){
                $callValue = $this->bank->getCallValue($player->info['id'], $this->step);
                $callSum = $this->bank->getCallSum($player->info['id'], $this->step);
                $status = array();
                if($player->isPlay()){
                    $status[] = 'play';
                }
                if($player->isFold()){
                    $status[] = 'fold';
                }
                if($player->isAllIn()){
                    $status[] = 'all in';
                }
                $str.= $position.') Player '.$player->info['id'].' / '.$player->info['username'].' (pos = '.$position.') '.(count($status)?'('.implode(', ',$status).')':'')."\n"
                    .' - cards: '.$player->cards."\n"
                    .' - balance: '.$player->getBalance()."\n"
                    .' - call need: '.$callValue."\n"
                    .' - call all: '.$callSum."\n";
            }
            $banks = $this->getBanks();
            $str.='Banks:'."\n".print_r($banks,true)."\n";
            $str.='turn position:'.$this->turnPosition."\n";
            $player = $this->getTurnPlayer();
            $str.="\n".(($player)?'Wait '.(($call)?'call':'check').'/raise/allIn from player '.$player->info['id']:'');
            
            return $str;
        }
        return 'Wait firsts players';
    }
    
    public function getCallSum()
    {
        return $this->bank->getBalance();
    }
}