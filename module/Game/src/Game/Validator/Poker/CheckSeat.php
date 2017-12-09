<?php

namespace Game\Validator\Poker;

use Game\Validator\AbstractValidator;

class CheckSeat extends AbstractValidator 
{
    protected $options = array(
    );
    
    protected $messageTemplates = array(
        'userArleadyInGame' =>  'Use arleady in game',
        'invalid'   =>  'invalid value',
        'busy'  =>  'Seat (%value%) is busy'
    );
    public function isValid($position)
    {
        $gameRow = $this->getFilter()->getGameRow();
        $gameRow->blockForUpdate();
        if($position<1 or $position>$gameRow->max_players){
            $this->error('invalid', $position);//self::ACCESS_DENIED);
            return false;
        }
        if($gameRow->players == $gameRow->max_players){
            $this->error('busy', $position);//self::ACCESS_DENIED);
            return false;
        }
        return true;
    }
    
    
}