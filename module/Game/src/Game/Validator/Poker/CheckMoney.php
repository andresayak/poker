<?php

namespace Game\Validator\Poker;

use Game\Validator\AbstractValidator;

class CheckMoney extends AbstractValidator 
{
    protected $options = array(
    );
    
    protected $messageTemplates = array(
        'low_raise' =>  'low raise',
        'you_dont_have_money' =>  'You dont have money',
    );
    public function isValid($position)
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
        return true;
    }
    
    
}