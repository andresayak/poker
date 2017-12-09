<?php

namespace Game\Model\Poker;

use Ap\Model\Row as Prototype;
use Game\Model\User\Row AS UserRow;

class Row extends Prototype
{
    protected $_user_rowset, $_cache_data, $_game;
    
    public function getGameCache(){
        return $this->getSm()->get('Poker\Cache\Storage');
    }
    
    public function getLogFilename()
    {
        return dirname(INDEX_PATH) . '/../data/poker/'.$this->id.'_'.date('Y-m-d_H:i', $this->time_start).'.txt';
    }
    
    public function enableLogger($output = true)
    {
        $logger = new \Zend\Log\Logger;
        if($output)
            $logger->addWriter('stream', null, array('stream' => 'php://output'));
        $logger->addWriter('stream', null, array('stream' => $this->getLogFilename()));
        $this->getGame()->setLogger($logger);
        return $this;
    }
    
    public function blockGame()
    {
        $redis = $this->getGameCache();
        $key = 'game_poker_players'.$this->id.'_block';
        $status = $redis->getConnection()->incrBy($key, 1);
        if($status!=1){
            $redis->getConnection()->incrBy($key, -1);
            throw new Exception\Blocked('is blocked');
        } 
    }
    
    public function unblockGame()
    {
        $redis = $this->getGameCache();
        $redis->getConnection()->del('game_poker_players'.$this->id.'_block');
    }
    
    public function updateBalance($position, $value)
    {
        $redis = $this->getGameCache();
        $key = 'game_poker_players'.$this->id.'_money_'.$position;
        $status = $redis->getConnection()->incrBy($key, $value);
        if($status<0){
            $redis->getConnection()->incrBy($key, -$value);
            throw new Exception\LowMoney('Money not have');
        } 
    }
    
    public function getGame()
    {
        if($this->_game === null){
            $blind = (int)$this->blind;
            $this->_game = new \Game\Model\Poker\Game(array(
                'max_player'    =>  $this->max_players,
                'blinds'        =>  array(
                    $blind, $blind*2
                )
            ));
            $this->enableLogger(false);
            $this->_game->setListener(function($params, $allOrUserId){
                if($allOrUserId === 'all'){
                    $channel = 'room';
                    $listeners = $this->getListeners();
                }else{
                    $channel = 'private';
                    $listeners = $allOrUserId;
                }
                $this->getSm()->get('PushCommet\Service')->send($params, $channel, $listeners);
            });
            $this->_game->setGameRow($this);
        }
        return $this->_game;
    }
    
    public function getCacheData()
    {
        if($this->_cache_data === null){
            $redis = $this->getGameCache();
            $key = 'game_poker_players'.$this->id;
            $this->_cache_data = $redis->getItem($key);
        }
        return $this->_cache_data;
    }
    
    public function saveCacheData()
    {
        $cacheData = $this->getGame()->getCacheData();
        $redis = $this->getGameCache();
        $key = 'game_poker_players'.$this->id;
        $redis->setItem($key, $cacheData);
    }
    
    public function run($function){
        $attem = 0;
        $max = 5;
        $status = true;
        while($attem<$max){
            try {
                $function();
            } catch (Exception\Blocked $e){
                unsleep(500000);
                $attem++;
                $status = false;
            } catch (Exception\ExceptionRule $e){
                throw new Exception\ExceptionRule($e->getMessage(), 0, $e);
                return;
            } catch (\Exception $e) {
                $this->unblockGame();
                throw new \Exception('addUser have exception', 0, $e);
            }
            if($status){
                return;
            }
        }
    }
    public function addUser(UserRow $userRow, $position, $money)
    {
        $this->run(function() use($userRow, $position, $money) {
            $seats = $this->getSeats();
            foreach ($seats AS $user_id) {
                if ($userRow->id == $user_id) {
                    throw new Exception\ExceptionRule('User arleady in game');
                }
            }
            $redis = $this->getGameCache();
            $key = 'game_poker_players' . $this->id . '_seat_' . $position;
            $seatStatus = $redis->getConnection()->incrBy($key, 1);
            if ($seatStatus != 1) {
                $redis->getConnection()->incrBy($key, -1);
                throw new Exception\ExceptionRule('Seat is busy');
            }
            $redis->getConnection()->set('game_poker_players' . $this->id . '_user_' . $position, $userRow->id);
            $redis->getConnection()->set('game_poker_players' . $this->id . '_money_' . $position, $money);
            $this->players = count($seats) + 1;

            $player = $this->getGame()->createPlayer($userRow->toArrayForApiBase(), $money);
            $this->addListen($userRow);
            $this->getGame()->join($player, $position);
            $this->saveCacheData();
            $redisChat = $this->getSm()->get('Chat\Cache\Storage');
            $redisChat->getConnection()->set('poker_user' . $userRow->id, $this->id);
            if (!$this->getGame()->isGameEnd) {
                $this->calcTimeOut();
            }
            $this->save();
        });
    }
    
    public function removeUserByPlayer($playerRow, $type = 'timeout')
    {
        $seats = $this->getSeats();
        $status = false;
        $redis = $this->getGameCache();
        $money = 0;
        foreach($seats AS $position=>$user_id){
            if($user_id == $playerRow->info['id']){
                $money = $redis->getConnection()->get('game_poker_players'.$this->id.'_money_'.$position);
                $redis->getConnection()->del('game_poker_players'.$this->id.'_seat_'.$position);
                $redis->getConnection()->del('game_poker_players'.$this->id.'_money_'.$position);
                $redis->getConnection()->del('game_poker_players'.$this->id.'_user_'.$position);
                $this->players = count($seats)-1;
                $status = true;
            }
        }
        if($status){
            $redisChat = $this->getSm()->get('Chat\Cache\Storage');
            $redisChat->getConnection()->del('poker_user' . $playerRow->info['id']);
            $this->getGame()->leave($playerRow, $type, $money);
            $this->saveCacheData();
            $this->save();
            return $money;
        }
        return false;
    }
    
    public function removeUser(UserRow $userRow)
    {
        $playerRow = $this->getGame()->getPlayerByUser($userRow);
        if(!$playerRow){
            return false;
        }
        $seats = $this->getSeats();
        $status = false;
        $redis = $this->getGameCache();
        $money = 0;
        foreach($seats AS $position=>$user_id){
            if($user_id == $userRow->id){
                $money = $redis->getConnection()->get('game_poker_players'.$this->id.'_money_'.$position);
                $redis->getConnection()->del('game_poker_players'.$this->id.'_seat_'.$position);
                $redis->getConnection()->del('game_poker_players'.$this->id.'_money_'.$position);
                $redis->getConnection()->del('game_poker_players'.$this->id.'_user_'.$position);
                $this->players = count($seats)-1;
                $status = true;
            }
        }
        if($status){
            $redisChat = $this->getSm()->get('Chat\Cache\Storage');
            $redisChat->getConnection()->del('poker_user' . $playerRow->info['id']);
            $this->getGame()->leave($playerRow, 'close', $money);
            $this->saveCacheData();
            $this->save();
            return $money;
        }
        return false;
    }
    
    public function getSeats() 
    {
        $redis = $this->getGameCache();
        $user_ids = array();
        for ($position = 1; $position <= $this->max_players; $position++) {
            $key = 'game_poker_players' . $this->id . '_user_' . $position;
            $id = $redis->getConnection()->get($key);
            if ($id) {
                $user_ids[$position] = $id;
            }
        }
        return $user_ids;
    }

    public function getListeners()
    {
        $redis = $this->getGameCache();
        $key = 'game_poker_players' . $this->id.'_listens';
        return $redis->getConnection()->SMEMBERS($key);
    }
    
    public function addListen(UserRow $userRow)
    {
        $redis = $this->getGameCache();
        $key = 'game_poker_players' . $this->id.'_listens';
        $redis->getConnection()->SADD($key, $userRow->id);
        return $this;
    }
    
    public function isListen(UserRow $userRow)
    {
        $redis = $this->getGameCache();
        $key = 'game_poker_players' . $this->id.'_listens';
        return $redis->getConnection()->SISMEMBER($key, $userRow->id);
    }
    
    public function removeListen(UserRow $userRow)
    {
        $redis = $this->getGameCache();
        $key = 'game_poker_players' . $this->id.'_listens';
        $redis->getConnection()->SREM($key, $userRow->id);
        return $this;
    }
    
    public function getUsers()
    {
        if($this->_user_rowset === null){
            $user_ids = $this->getSeats();
            if(count($user_ids)){
                $this->_user_rowset = $this->getSm()->get('User\Table')->fetchAllByIds($user_ids);
            }else{
                $this->_user_rowset = false;
            }
        }
        return $this->_user_rowset;
    }
    
    public function toArrayForApi($userRow = false)
    {
        $data = $this->toArray();
        unset($data['password']);
        $data['seats']=$this->getSeats();
        $data['data'] = $this->getGame()->toArrayForApi($userRow);
        $data['listeners']  = $this->getListeners();
        return $data;
    }
    
    public function call(UserRow $userRow)
    {
        if($this->getGame()->isGameEnd){
            return $this;
        }
        
        $this->run(function() use($userRow){
            $playerRow = $this->getGame()->getPlayerByUser($userRow);
            if($playerRow){
                $this->getGame()->call($playerRow);
                $this->calcTimeOut();
                $this->saveCacheData();
                $this->save();
            }
        });
        return $this;
    }
    
    public function check(UserRow $userRow)
    {
        if($this->getGame()->isGameEnd){
            return $this;
        }
        $this->run(function() use($userRow){
            $playerRow = $this->getGame()->getPlayerByUser($userRow);
            if($playerRow){
                $this->getGame()->check($playerRow);
                $this->calcTimeOut();
                $this->saveCacheData();
                $this->save();
            }
        });
        return $this;
    }
    
    public function allin(UserRow $userRow)
    {
        if($this->getGame()->isGameEnd){
            return $this;
        }
        $this->run(function() use($userRow){
            $playerRow = $this->getGame()->getPlayerByUser($userRow);
            if($playerRow){
                $this->getGame()->allin($playerRow);
                $this->calcTimeOut();
                $this->saveCacheData();
                $this->save();
            }
        });
        return $this;
    }
    
    public function fold(UserRow $userRow)
    {
        if($this->getGame()->isGameEnd){
            return $this;
        }
        $this->run(function() use($userRow){
            $playerRow = $this->getGame()->getPlayerByUser($userRow);
            if($playerRow){
                $this->getGame()->fold($playerRow);
                $this->calcTimeOut();
                $this->saveCacheData();
                $this->save();
            }
        });
        return $this;
    }
    
    public function clear()
    {
        $this->run(function(){
            $redisChat = $this->getSm()->get('Chat\Cache\Storage');
            $redis = $this->getGameCache();
            for ($position = 1; $position <= $this->max_players; $position++) {
                $user_id = $redis->getConnection()->get('game_poker_players'.$this->id.'_user_'.$position);
                $redis->getConnection()->del('game_poker_players'.$this->id.'_user_'.$position);
                $redis->getConnection()->del('game_poker_players'.$this->id.'_seat_'.$position);
                $redis->getConnection()->del('game_poker_players'.$this->id.'_money_'.$position);
                if($user_id)
                    $redisChat->getConnection()->del('poker_user' . $user_id);
            }
            $redis->getConnection()->del('game_poker_players' . $this->id.'_listens');
            $redis->getConnection()->del('game_poker_players' . $this->id);
                    
            $this->getGame()->clear();
            $this->setFromArray(array(
                'players'   =>  0,
                'status'    =>  0,
                'time_start'=>  null
            ));
            $this->saveCacheData();
            $this->save();
            @unlink($this->getLogFilename());
        });
        return $this;
    }
    
    public function timeout()
    {
        if(!$this->getGame()->isGameEnd){
            $result = false;
            $this->run(function() use(&$result){
                if($playerRow =  $this->getGame()->getTurnPlayer()){
                    $money = $this->getGame()->timeout($playerRow);
                    $this->calcTimeOut();
                    $this->saveCacheData();
                    $this->save();
                    if ($money) {
                        $result = array(
                            'money' => $money,
                            'user_id' => $playerRow->info['id']
                        );
                    }
                }
            });
            return $result;
        }
        return false;
    }
    
    public function start()
    {
        if($this->getGame()->isGameEnd){
            $this->run(function(){
                $this->getGame()->start();
                $this->calcTimeOut();
                $this->saveCacheData();
                $this->save();
            });
        }
    }
    
    public function calcTimeOut()
    {
        $this->time_update = time()+$this->getGame()->getOption('time');
    }
    
    public function raise(UserRow $userRow, $value)
    {
        if($this->getGame()->isGameEnd){
            return $this;
        }
        $this->run(function() use($userRow, $value){
            $playerRow = $this->getGame()->getPlayerByUser($userRow);
            if($playerRow){
                $this->getGame()->raise($playerRow, $value);
                $this->calcTimeOut();
                $this->saveCacheData();
                $this->save();
            }
        });
        return $this;
    }
}