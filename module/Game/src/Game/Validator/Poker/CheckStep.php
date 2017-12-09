<?php

namespace Game\Validator\Poker;

use Game\Validator\AbstractValidator;

class CheckStep extends AbstractValidator 
{
    protected $options = array(
    );
    
    protected $messageTemplates = array(
        'not_your_step' =>  'Not your step',
        'you_are_fold'  =>  'You are fold',
        'you_not_play'  =>  'You not play',
        'you_are_allin' =>  'You are all in',
        'game_not_star' =>  'Game not start'
    );
    public function isValid($value)
    {
        $gameRow = $this->getFilter()->getGameRow();
        if(!$gameRow->getGame()->getPlayerByUser($this->getFilter()->getUserRow())){
            $this->error('you_not_play');
            return false;
        }
        if($gameRow->getGame()->isGameEnd){
            $this->error('game_not_star');
            return false;
        }
        $player = $gameRow->getGame()->getTurnPlayer();
        if($player->info['id'] != $this->getFilter()->getUserRow()->id){
            $this->error('not_your_step');
            return false;
        }
        if($player->isFold()){
            $this->error('you_are_fold');
            return false;
        }
        if(!$player->isPlay()){
            $this->error('you_not_play');
            return false;
        }
        if($player->isAllIn()){
            $this->error('you_are_allin');
            return false;
        }
        return true;
    }
    
    
}