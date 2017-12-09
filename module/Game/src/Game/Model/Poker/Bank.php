<?php

namespace Game\Model\Poker;

use Zend\Log\Logger;

class Bank
{
    protected $_data;
    protected $logger;
    
    public function __construct() 
    {
        $this->reset();
        $this->logger = new Logger;
        $this->logger->addWriter('Null');
        return $this;
    }
    
    public function __sleep() 
    {
        return array('_data');
    }
    
    public function setLogger(Logger $logger)
    {
        $this->logger = $logger;
        return $this;
    }
    
    public function getBanks($positions)
    {
        $callsByPos = array();
        $sumCalls = 0;
        foreach($this->_data['steps'] AS $items){
            foreach($items AS $user_id=>$value){
                $status = false;
                foreach($positions AS $position=>$player){
                    if($player->info['id'] == $user_id){
                        if(!isset($callsByPos[$position])){
                            $callsByPos[$position] = 0;
                        }
                        $callsByPos[$position]+=$value;
                        $status = true;
                    }
                }
                if(!$status){
                    if(!isset($callsByPos[-$user_id])){
                        $callsByPos[-$user_id] = 0;
                    }
                    $callsByPos[-$user_id]+=$value;
                }
                $sumCalls+=$value;
            }
        }
        $maxCall = max($callsByPos);
        $allins = array_unique(array_values($this->_data['allins']));
        if(count($allins)){
            $maxAllin = max($allins);
            if($maxAllin<$maxCall){
                $allins[] = $maxCall;
            }
        }else{
            $allins[] = $maxCall;
        }
        asort($allins);
        $banks = array();
        if(isset($allins[0]) and count($allins)==1){
            $banks[0] = array(
                'money' => $sumCalls,
                'allin' => $allins[0],
                'positions' => array_keys($callsByPos)
            );
        }else{
            $prev = 0;
            $allinsKeys = array_keys($allins);
            
            $prevAllin = 0;
            foreach($allins AS $index=>$allinValue){
                $prev = 0;
                foreach($callsByPos AS $pos=>$value){
                    if($value>=$prevAllin and $value < $allinValue){
                        $prev+=$value-$prevAllin;
                    }
                }
                $bank = array(
                    'money'  =>  $prev,
                    'allin' =>  $allinValue,
                    'positions' =>  array()
                );
                
                foreach($callsByPos AS $pos=>$value){
                    if($value>=$allinValue){
                        $bank['money']+=$allinValue-$prevAllin;
                        if($pos>0)
                            $bank['positions'][]=$pos;
                    }
                }
                
                $banks[] = $bank;
                $prevAllin = $allinValue;
            }
        }
        $sumWin = 0;
        foreach($banks AS $bank){
            $sumWin+=$bank['money'];
        }
        if($sumWin!=$sumCalls){
            echo '$steps = '.print_r($this->_data['steps'], true)."\n";
            echo '$allins = '.print_r($allins,true)."\n";
            echo '$banks = '.print_r($banks,true)."\n";
            echo '$sumCalls = '.$sumCalls."\n";
            echo '$sumWin = '.$sumWin."\n";
            echo '$bank = '.$this->getBalance()."\n";
            throw new \Exception('invlaid banks ');
        }
        return $banks;
    }

    public function add($value, $player, $step, $allin = false)
    {
        if($allin){
            $this->_data['allins'][$player->info['id']] = $this->getCallAllSum($player->info['id']) + $value;
        }
        $this->_data['main']['balance']+= $value;
        if(!isset($this->_data['steps'][$step])){
            $this->_data['steps'][$step] = array();
        }
        if(!isset($this->_data['steps'][$step][$player->info['id']])){
            $this->_data['steps'][$step][$player->info['id']] = 0;
        }
        $this->_data['steps'][$step][$player->info['id']]+= $value;
        return $this;
    }
    
    public function getWin($player)
    {
        $balance = $this->getBalance();
        $player->updateBalance($balance);
        return $balance;
    }
    
    public function getCallStep($step)
    {
        $max = 0;
        if(isset($this->_data['steps'][$step])){
            foreach($this->_data['steps'][$step] AS $value){
                $max = max($max, $value);
            }
        }
        return $max;
    }
    
    public function getCallValue($player_id, $step)
    {
        $max = 0;
        if(isset($this->_data['steps'][$step])){
            foreach($this->_data['steps'][$step] AS $value){
                $max = max($max, (int)$value);
            }
            if(isset($this->_data['steps'][$step][$player_id])){
                $max-= (int)$this->_data['steps'][$step][$player_id];
            }
        }
        return $max;
    }
    
    public function getCallSum($player_id, $step)
    {
        if(isset($this->_data['steps'][$step])){
            if(isset($this->_data['steps'][$step][$player_id])){
                return $this->_data['steps'][$step][$player_id];
            }
        }
        return 0;
    }
    
    public function getCallAllSum($player_id)
    {
        $value = 0;
        foreach($this->_data['steps'] AS $players){
            if(isset($players[$player_id])){
                $value+= $players[$player_id];
            }
        }
        return $value;
    }
    
    public function isCompared($players, $step)
    {
        foreach ($players AS $player) {
            $player_id = (int)$player->info['id'];
            if ($player->isFold() || !$player->isPlay() || $player->isAllIn()) {
                continue;
            }
            $callValue = $this->getCallValue($player_id, $step);
            if (!isset($this->_data['steps'][$step])
                or ! isset($this->_data['steps'][$step][$player_id])
                or $callValue
            ) {
                return false;
            }
        }
        return true;
    }
    
    public function reset()
    {
        $this->_data = array(
            'main'  =>  array(
                'balance'   =>  0
            ),
            'banks'     =>  array(),
            'steps'     =>  array(),
            'allins'    =>  array(),
        );
        return $this;
    }
    
    public function getBalance()
    {
        return $this->_data['main']['balance'];
    }
    
    public function toArrayForApi()
    {
        return $this->_data;
    }
}