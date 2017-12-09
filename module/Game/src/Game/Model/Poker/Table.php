<?php

namespace Game\Model\Poker;

use Ap\Model\Table as Prototype;

class Table extends Prototype
{
    protected $_name = 'poker';
    protected $_cols = array(
        'id', 'max_players', 'blind', 
        'players', 'time_update', 'time_start', 'user_id', 'password', 'status'
    );
    
    protected $_defaults = array(
        'status'        =>  0,
        'players'       =>  0,
        'time_start'    =>  null,
        'time_update'   =>  null,
        'password'      =>  null
    );
}