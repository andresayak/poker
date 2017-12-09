<?php

namespace GameTest\Controller;

use GameTest\ControllerTest;
use Game\Model\Poker\Exception\ExceptionRule;

class PokerControllerTest extends ControllerTest
{
    //TODO 
    //вихід ігрока підчас ігри
    //таймауд
    
    public function testStop(){
        
        $game = $this->_createGame();
        $game->setOptions(array(
            'blinds'    =>  array(1, 2)
        ));
        
        $player1 = $game->createPlayer(array(
            'id'        =>  '1000',
            'username'  =>  'Nu Go'
        ), 246);
        $game->join($player1, 1);
        
        $player2 = $game->createPlayer(array(
            'id'        =>  '2000',
            'username'  =>  'Любомир Ковальов'
        ), 259);
        $game->join($player2, 2);
        
        $player6 = $game->createPlayer(array(
            'id'        =>  '3000',
            'username'  =>  'Anna Zonova'
        ), 35);
        $game->join($player6, 6);
        
        $game->dealerPosition = 1;
        $game->start();       
        
        $game->call($player2);
        $game->call($player6);//isCompared true
        $game->check($player1);//isCompared true, isAllSay true
        //Step 1
        $game->leave($player1, 'close');
        
        $game->check($player6);
        $game->check($player2);//isCompared true, isAllSay true
        //Step 2
        $game->check($player6);
        $game->check($player2);//isCompared true, isAllSay true
        //Step 3
        $game->check($player6);
        $game->check($player2);//isCompared true, isAllSay true
        $this->assertTrue($game->isGameEnd);
        //Game end
        exit;
    }
    
    public function testFreeze3(){
        
        $game = $this->_createGame();
        $game->setOptions(array(
            'blinds'    =>  array(1, 2)
        ));
        
        
        
        $player2 = $game->createPlayer(array(
            'id'        =>  '1000',
            'username'  =>  'Ігор Васильович'
        ), 268482);
        $game->join($player2, 2);
        
        $player5 = $game->createPlayer(array(
            'id'        =>  '2000',
            'username'  =>  'Андрій Васильович'
        ), 9976255);
        $game->join($player5, 5);
        
        $player6 = $game->createPlayer(array(
            'id'        =>  '3000',
            'username'  =>  'Любомир Ковальов'
        ), 200023438);
        $game->join($player6, 6);
        
        $player7 = $game->createPlayer(array(
            'id'        =>  '4000',
            'username'  =>  'Микола Миколайович'
        ), 1036);
        $game->join($player7, 7);
        
        $game->dealerPosition = 7;
        $game->start();       
        
        $game->call($player7);
        $game->raise($player2, 49972);       
        $game->leave($player2, 'close');
        $game->call($player6);
        $game->fold($player6);
        $game->allin($player7);
        $game->fold($player5);
    }
    
    public function testFreeze2(){
        
        $game = $this->_createGame();
        $game->setOptions(array(
            'blinds'    =>  array(1, 2)
        ));
        
        $player2 = $game->createPlayer(array(
            'id'        =>  '1000',
            'username'  =>  'Микола Миколайович'
        ), 8756);
        $game->join($player2, 2);
        
        $player4 = $game->createPlayer(array(
            'id'        =>  '2000',
            'username'  =>  'Андрій Васильович'
        ), 9995);
        $game->join($player4, 4);
        
        $player6 = $game->createPlayer(array(
            'id'        =>  '3000',
            'username'  =>  'Любомир Ковальов'
        ), 6863);
        $game->join($player6, 6);
                
        $game->dealerPosition = 6;
        
        $game->start();       
        
        $game->call($player2);
        $game->call($player4);        
        $game->check($player6);
        
        $game->check($player2); 
        $game->check($player4); 
        
        $game->allin($player6);
        $game->call($player2);
        $game->raise($player4, 7242);
        $game->call($player2);
        $game->check($player4);
        $game->check($player2);
        $game->leave($player2, 'close');
        $this->assertTrue($game->isGameEnd);
        
    }
    
    public function testFreeze(){
        
        $game = $this->_createGame();
        $game->setOptions(array(
            'blinds'    =>  array(1, 2)
        ));
        
        $player1 = $game->createPlayer(array(
            'id'        =>  '1000',
            'username'  =>  'Микола Миколайович'
        ), 155);
        $game->join($player1, 1);
        
        $player2 = $game->createPlayer(array(
            'id'        =>  '2000',
            'username'  =>  'Любомир Ковальов'
        ), 2000);
        $game->join($player2, 2);
        
        $player3 = $game->createPlayer(array(
            'id'        =>  '3000',
            'username'  =>  'Ігор Васильович'
        ), 6465);
        $game->join($player3, 3);
                
        $player5 = $game->createPlayer(array(
            'id'        =>  '5000',
            'username'  =>  'Андрій Васильович'
        ), 1003);
        $game->join($player5, 5);
        
        
        $game->dealerPosition = 3;
        
        $game->start();       
        
        $game->call($player3);
        $game->call($player5);        
        $game->call($player1);        
        $game->allin($player2);  
        
        $error = false;
        try{
            $game->leave($player2);
        } catch (ExceptionRule $e) {
            $error = $e->getMessage();
        }
        $this->assertEquals($error, 'player in allin');
        
        
        $game->allin($player3);        
        $game->fold($player1);        
        $game->fold($player5);  
        $this->assertTrue($game->isGameEnd);
    }
        
//public cards: 7clubs, queenclubs, 10clubs, 7diamonds, 6spades
//player  = Микола Миколайович (1) cards [3diamonds, 3hearts] use [7clubs(R), 7diamonds(R)] ranking [two_pair] weight 73008
//player  = Андрій Васильович (2) cards [queenhearts, 3spades] use [7clubs(R), queenclubs(R)] ranking [two_pair] weight 85553
//player  = Володимир Коновалюк (3) cards [acediamonds, aceclubs] use [7clubs(R), 7diamonds(R)] ranking [two_pair] weight 74867
//player  = Любомир Ковальов (4) cards [jackspades, 5clubs] use [7clubs(R), 7diamonds(R)] ranking [one_pair] weight 45864
//player  = Ігор Васильович is fold
//win Андрій Васильович = 10
//balance Микола Миколайович = 2716
//balance Андрій Васильович = 2379
//balance Володимир Коновалюк = 16505
//balance Любомир Ковальов = 2823
//balance Ігор Васильович = 496
//callSum = 10
//bankSum = 10
    public function testWin2()
    {
        $game = $this->_createGame();
        $values = array(
            'public'    =>  '7/clubs, queen/clubs, 10/clubs, 7/diamonds, 6/spades',
            'private1'   =>  '3/diamonds, 3/hearts',
            'private2'   =>  'jack/spades, 5/clubs',
            'private3'   =>  'ace/diamonds, ace/clubs',
            'private4'   =>  'queen/hearts, 3/spades',
        );
        $count = 4;
        $players = array();
        for($i=1;$i<=$count;$i++){
            $players[$i] = $game->createPlayer(array(
                'id' => $i,
                'username' => 'Player'.$i
            ), 1000+$i);
            $game->join($players[$i], $i);
        }

        $game->start();
        for($i=1;$i<=$count;$i++){
            $players[$i]->setCards($game->rule->createCardDesc($values['private'.$i]));
        }
        $game->setPublicCards($game->rule->createCardDesc($values['public']));
        $wins = $game->calcWins();
        $this->assertEquals(count($wins[0]['positions']), 1);
        $this->assertTrue(isset($wins[0]['positions'][3]));
        //exit;
    }
    
    //public: kinghearts, 10spades, 2clubs, 9spades, 7diamonds
    //Андрій Васильович take: king♦, queen♣
    //Iulia Parascheev take: ace♥, 10♣
    //Ігор Васильович take: 10♦, ace♣
    //Vasya Doutchak take: ace♠, 5♥
    
    public function testBank2(){
        
        $game = $this->_createGame();
        $game->setOptions(array(
            'blinds'    =>  array(1, 2)
        ));
        
        $player1 = $game->createPlayer(array(
            'id'        =>  '1000',
            'username'  =>  'Любомир Ковальов'
        ), 2309);
        $game->join($player1, 1);
        
        $player2 = $game->createPlayer(array(
            'id'        =>  '2000',
            'username'  =>  'Микола Миколайович'
        ), 7438);
        $game->join($player2, 2);
        
        $player3 = $game->createPlayer(array(
            'id'        =>  '3000',
            'username'  =>  'Володимир Коновалюк'
        ), 7413);
        $game->join($player3, 3);
        
        $player4 = $game->createPlayer(array(
            'id'        =>  '4000',
            'username'  =>  'Андрій Васильович'
        ), 824);
        $game->join($player4, 4);
        
        $player7 = $game->createPlayer(array(
            'id'        =>  '7000',
            'username'  =>  'Vasya Doutchak'
        ), 1037);
        $game->join($player7, 7);
        
        $player8 = $game->createPlayer(array(
            'id'        =>  '8000',
            'username'  =>  'Ігор Васильович'
        ), 3014);
        $game->join($player8, 8);
        
        
        $game->dealerPosition = 4;        
        $game->start();       
        
        $game->call($player2);
        $game->call($player3);        
        $game->call($player4);        
        $game->call($player7);
        $game->call($player8);
        $game->check($player1);
        $game->check($player2);
        $game->check($player3);
        $game->check($player4);
        $game->check($player7);
        $game->raise($player8, 50);
        $game->call($player1);
        $game->call($player2);
        $game->call($player3);
        $game->call($player4);
        $game->call($player7);
        $game->fold($player7);
        $game->check($player8);
        $game->allin($player1);
        $game->raise($player2, 2897);
        $game->call($player3);
        $game->fold($player4);
        $game->fold($player8);
        //Step 3
        $game->check($player2);
        $game->raise($player3, 1223);
        $game->fold($player2);
        //Stop step 3
        $this->assertTrue($game->isGameEnd);
    }
    
    public function testAllIn4Win()
    {
        $game = $this->_createGame();
        $players = array();
        for($i = 1; $i<=10;$i++){
            $players[$i] = $game->createPlayer(array(
                'id'        =>  $i,
                'username'  =>  'Player'.$i
            ), 100+$i*10);
            $game->join($players[$i], $i);
        }

        $game->start();
        
        $start = $game->turnPosition;
        for($i = 0; $i<9;$i++){
            $pos = $start+$i;
            $pos = ($pos>10)?($pos)%10:$pos;
            $game->call($players[$pos]);
        }
        $game->check($players[3]);
        
        $this->assertEquals($game->getOption('public_cards'), $game->publicCards->count());
        
        $game->raise($players[4], 20);
        $game->call($players[5]);
        $game->call($players[6]);
        $game->call($players[7]);
        $game->call($players[8]);
        $game->call($players[9]);
        $game->call($players[10]);
        $game->allin($players[1]);
        $game->call($players[2]);
        $game->call($players[3]);
        $game->call($players[4]);
        $game->call($players[5]);
        $game->call($players[6]);
        $game->fold($players[7]);
        $game->fold($players[8]);
        $game->call($players[9]);
        $game->call($players[10]);
        
        $this->assertEquals($game->getOption('public_cards')+1, $game->publicCards->count());

        $game->check($players[2]);
        $game->allin($players[3]);
        $game->call($players[4]);
        $game->call($players[5]);
        $game->fold($players[2]);
        $game->fold($players[10]);
        $game->call($players[6]);
        $game->call($players[9]);
        $game->allin($players[4]);
        $game->allin($players[5]);
        
        $game->call($players[6]);
        $game->call($players[9]);
        
        $this->assertEquals($game->getOption('public_cards')+2, $game->publicCards->count());
        $this->assertTrue($game->isGameEnd);
        
        $banks = $game->getBanks();
        $this->assertEquals(4, count($banks));
    }
    
    public function testWin()
    {
        $game = $this->_createGame();
        $values = array(
            'public'    =>  'king/hearts, 10/spades, 2/clubs, 9/spades, 7/diamonds',
            'private1'   =>  'king/diamonds, queen/clubs',
            'private2'   =>  'ace/hearts, 10/clubs',
            //'private3'   =>  '10/diamonds, ace/clubs',
            //'private4'   =>  'ace/spades, 5/hearts'
        );
        $count = 2;
        $players = array();
        for($i=1;$i<=$count;$i++){
            $players[$i] = $game->createPlayer(array(
                'id' => $i,
                'username' => 'Player'.$i
            ), 1000);
            $game->join($players[$i], $i);
        }

        $game->start();
        for($i=1;$i<=$count;$i++){
            $players[$i]->setCards($game->rule->createCardDesc($values['private'.$i]));
        }
        $game->setPublicCards($game->rule->createCardDesc($values['public']));
        $wins = $game->calcWins();
        //print_r($wins);exit;
        //$this->assertEquals(count($wins[0]['positions']), 2);
        //exit;
    }
   
    public function testRankingRoyalFlush()
    {
        $game = $this->_createGame();
        $values = array(
            'public'    =>  'ace/diamonds, king/diamonds, jack/diamonds, 6/hearts, 2/spades',
            'private'   =>  'queen/diamonds, 10/diamonds'
        );
        $publicCards = $game->rule->createCardDesc($values['public']);
        $handCard = $game->rule->createCardDesc($values['private']);
        $cards = $game->rule->calcWeight($handCard, $publicCards);
        $this->assertEquals($cards->getRanking(), 'royal_flush');
    }
    
    public function testRankingStraightFlush()
    {
        $game = $this->_createGame();
        $values = array(
            'public'    =>  '4/diamonds, 5/diamonds, 2/diamonds, 6/hearts, 2/spades',
            'private'   =>  '2/diamonds, 3/diamonds'
        );
        $publicCards = $game->rule->createCardDesc($values['public']);
        $handCard = $game->rule->createCardDesc($values['private']);
        $cards = $game->rule->calcWeight($handCard, $publicCards);
        $this->assertEquals($cards->getRanking(), 'straight_flush');
    }
    
    public function testRankingStraightFlush_2Ace()
    {   
        $game = $this->_createGame();
        $values = array(
            'public'    =>  '4/diamonds, 5/diamonds, 2/hearts, 3/diamonds, 2/spades',
            'private'   =>  '2/diamonds, ace/diamonds'
        );
        $publicCards = $game->rule->createCardDesc($values['public']);
        $handCard = $game->rule->createCardDesc($values['private']);
        $cards = $game->rule->calcWeight($handCard, $publicCards);
        $this->assertEquals($cards->getRanking(), 'straight_flush');
    }
    
    public function testRankingFourOfAKind()
    {   
        $game = $this->_createGame();
        $values = array(
            'public'    =>  '4/diamonds, 5/spades, 4/hearts, 3/hearts, 4/clubs',
            'private'   =>  '2/diamonds, 4/spades'
        );
        $publicCards = $game->rule->createCardDesc($values['public']);
        $handCard = $game->rule->createCardDesc($values['private']);
        $cards = $game->rule->calcWeight($handCard, $publicCards);
        $this->assertEquals($cards->getRanking(), 'four_kind');
    }
    
    public function testRankingFullHouse()
    {   
        $game = $this->_createGame();
        $values = array(
            'public'    =>  '4/diamonds, 5/diamonds, 4/hearts, 3/hearts, 4/clubs',
            'private'   =>  '2/diamonds, 5/spades'
        );
        $publicCards = $game->rule->createCardDesc($values['public']);
        $handCard = $game->rule->createCardDesc($values['private']);
        $cards = $game->rule->calcWeight($handCard, $publicCards);
        $this->assertEquals($cards->getRanking(), 'full_house');
    }
    
    public function testRankingFlush()
    {   
        $game = $this->_createGame();
        $values = array(
            'public'    =>  '4/diamonds, 5/diamonds, king/hearts, 9/diamonds, ace/diamonds',
            'private'   =>  '2/diamonds, 7/spades'
        );
        $publicCards = $game->rule->createCardDesc($values['public']);
        $handCard = $game->rule->createCardDesc($values['private']);
        $cards = $game->rule->calcWeight($handCard, $publicCards);
        $this->assertEquals($cards->getRanking(), 'flush');
    }
    
    public function testRankingStraight()
    {   
        $game = $this->_createGame();
        $values = array(
            'public'    =>  '4/diamonds, 5/spades, 2/hearts, 3/hearts, 2/spades',
            'private'   =>  '2/diamonds, ace/spades'
        );
        $publicCards = $game->rule->createCardDesc($values['public']);
        $handCard = $game->rule->createCardDesc($values['private']);
        $cards = $game->rule->calcWeight($handCard, $publicCards);
        $this->assertEquals($cards->getRanking(), 'straight');
    }
    
    public function testRankingThreeOfAKind()
    {   
        $game = $this->_createGame();
        $values = array(
            'public'    =>  'ace/hearts, 5/spades, 7/hearts, 3/hearts, 2/spades',
            'private'   =>  'ace/diamonds, ace/spades'
        );
        $publicCards = $game->rule->createCardDesc($values['public']);
        $handCard = $game->rule->createCardDesc($values['private']);
        $cards = $game->rule->calcWeight($handCard, $publicCards);
        $this->assertEquals($cards->getRanking(), 'three_kind');
    }
    
    public function testRankingTwoPair()
    {   
        $game = $this->_createGame();
        $values = array(
            'public'    =>  'ace/hearts, 5/spades, 7/hearts, 5/hearts, 2/spades',
            'private'   =>  'ace/diamonds, king/spades'
        );
        $publicCards = $game->rule->createCardDesc($values['public']);
        $handCard = $game->rule->createCardDesc($values['private']);
        $cards = $game->rule->calcWeight($handCard, $publicCards);
        $this->assertEquals($cards->getRanking(), 'two_pair');
    }
    
    public function testRankingOnePair()
    {   
        $game = $this->_createGame();
        $values = array(
            'public'    =>  'ace/hearts, 5/spades, 7/hearts, 8/hearts, 2/spades',
            'private'   =>  'ace/diamonds, 3/spades'
        );
        $publicCards = $game->rule->createCardDesc($values['public']);
        $handCard = $game->rule->createCardDesc($values['private']);
        $cards = $game->rule->calcWeight($handCard, $publicCards);
        $this->assertEquals($cards->getRanking(), 'one_pair');
    }
    
    public function testRankingWeight()
    {   
        $game = $this->_createGame();
        $values = array(
            'public'    =>  'king/hearts, 5/spades, 7/hearts, 8/hearts, 2/spades',
            'playerA'   =>  'ace/diamonds, 3/spades',
            'playerB'   =>  'ace/diamonds, 3/spades'
        );
        $publicCards = $game->rule->createCardDesc($values['public']);
        $handACard = $game->rule->createCardDesc($values['playerA']);
        $handBCard = $game->rule->createCardDesc($values['playerB']);
        
        $cardsA = $game->rule->calcWeight($handACard, $publicCards);
        $cardsB = $game->rule->calcWeight($handBCard, $publicCards);
        
        $this->assertTrue($cardsA->getWeight() == $cardsB->getWeight());
    }
    
    public function testRankingWeightA()
    {   
        $game = $this->_createGame();
        $values = array(
            'public'    =>  'king/hearts, 5/spades, 7/hearts, 8/hearts, 2/spades',
            'playerA'   =>  'ace/diamonds, 4/spades',
            'playerB'   =>  'ace/diamonds, 3/spades'
        );
        $publicCards = $game->rule->createCardDesc($values['public']);
        $handACard = $game->rule->createCardDesc($values['playerA']);
        $handBCard = $game->rule->createCardDesc($values['playerB']);
        
        $cardsA = $game->rule->calcWeight($handACard, $publicCards);
        $cardsB = $game->rule->calcWeight($handBCard, $publicCards);
        
        $this->assertTrue($cardsA->getWeight() > $cardsB->getWeight(), $cardsA->getWeight().' < '.$cardsB->getWeight());
    }
    
    public function testRankingWeightB()
    {   
        $game = $this->_createGame();
        $values = array(
            'public'    =>  'king/hearts, 5/spades, 7/hearts, 8/hearts, 2/spades',
            'playerA'   =>  'ace/diamonds, 4/spades',
            'playerB'   =>  'ace/diamonds, 9/spades'
        );
        $publicCards = $game->rule->createCardDesc($values['public']);
        $handACard = $game->rule->createCardDesc($values['playerA']);
        $handBCard = $game->rule->createCardDesc($values['playerB']);
        
        $cardsA = $game->rule->calcWeight($handACard, $publicCards);
        $cardsB = $game->rule->calcWeight($handBCard, $publicCards);
        
        $this->assertTrue($cardsA->getWeight() < $cardsB->getWeight(), $cardsA->getWeight().' < '.$cardsB->getWeight());
    }
    public function testRankingWeightOnePairA()
    {   
        $game = $this->_createGame();
        $values = array(
            'public'    =>  'ace/hearts, 5/spades, 7/hearts, 9/hearts, 2/spades',
            'playerA'   =>  '9/hearts, king/spades',
            'playerB'   =>  '9/diamonds, 10/spades'
        );
        $publicCards = $game->rule->createCardDesc($values['public']);
        $handACard = $game->rule->createCardDesc($values['playerA']);
        $handBCard = $game->rule->createCardDesc($values['playerB']);
        
        $cardsA = $game->rule->calcWeight($handACard, $publicCards);
        $cardsB = $game->rule->calcWeight($handBCard, $publicCards);
        
        $this->assertEquals($cardsA->getRanking(), 'one_pair');
        $this->assertEquals($cardsB->getRanking(), 'one_pair');
        
        $this->assertTrue($cardsA->getWeight() > $cardsB->getWeight(), $cardsA->getWeight().' > '.$cardsB->getWeight());
    }
    
    public function testRankingWeightOnePairB()
    {   
        $game = $this->_createGame();
        $values = array(
            'public'    =>  'ace/hearts, 5/spades, 7/hearts, 9/hearts, 2/spades',
            'playerA'   =>  '9/hearts, 3/spades',
            'playerB'   =>  '9/diamonds, 10/spades'
        );
        $publicCards = $game->rule->createCardDesc($values['public']);
        $handACard = $game->rule->createCardDesc($values['playerA']);
        $handBCard = $game->rule->createCardDesc($values['playerB']);
        
        $cardsA = $game->rule->calcWeight($handACard, $publicCards);
        $cardsB = $game->rule->calcWeight($handBCard, $publicCards);
        
        $this->assertEquals($cardsA->getRanking(), 'one_pair');
        $this->assertEquals($cardsB->getRanking(), 'one_pair');
        
        $this->assertTrue($cardsA->getWeight() < $cardsB->getWeight(), $cardsA->getWeight().' < '.$cardsB->getWeight());
    }
    
    public function testRankingWeightFlushA()
    {   
        $game = $this->_createGame();
        $values = array(
            'public'    =>  'ace/hearts, 5/hearts, 7/spades, 9/hearts, 2/spades',
            'playerA'   =>  '9/hearts, 3/hearts',
            'playerB'   =>  '9/spades, 10/spades'
        );
        $publicCards = $game->rule->createCardDesc($values['public']);
        $handACard = $game->rule->createCardDesc($values['playerA']);
        $handBCard = $game->rule->createCardDesc($values['playerB']);
        
        $cardsA = $game->rule->calcWeight($handACard, $publicCards);
        $cardsB = $game->rule->calcWeight($handBCard, $publicCards);
        
        $this->assertEquals($cardsA->getRanking(), 'flush');
        $this->assertEquals($cardsB->getRanking(), 'one_pair');
        
        $this->assertTrue($cardsA->getWeight() > $cardsB->getWeight(), $cardsA->getWeight().' < '.$cardsB->getWeight());
    }
    
    public function testRankingWeightFlushB()
    {   
        $game = $this->_createGame();
        $values = array(
            'public'    =>  'ace/hearts, king/diamonds, 7/diamonds, 9/diamonds, 2/diamonds',
            'playerA'   =>  'ace/spades, 4/diamonds',
            'playerB'   =>  '4/spades, ace/diamonds'//winner
        );
        $publicCards = $game->rule->createCardDesc($values['public']);
        $handACard = $game->rule->createCardDesc($values['playerA']);
        $handBCard = $game->rule->createCardDesc($values['playerB']);
        
        $cardsA = $game->rule->calcWeight($handACard, $publicCards);
        $cardsB = $game->rule->calcWeight($handBCard, $publicCards);
        
        $this->assertEquals($cardsA->getRanking(), 'flush');
        $this->assertEquals($cardsB->getRanking(), 'flush');
        
        $this->assertTrue($cardsA->getWeight() < $cardsB->getWeight(), $cardsA->getWeight().' < '.$cardsB->getWeight());
    }
    
     public function testRankingWeightTwoPair1()
    {   
        $game = $this->_createGame();
        $values = array(
            'public'    =>  'ace/spades, 2/diamonds, 3/hearts, 9/clubs, 5/diamonds',
            'playerA'   =>  '5/spades, 9/diamonds',
            'playerB'   =>  '2/clubs, ace/hearts'//winner
        );
        $publicCards = $game->rule->createCardDesc($values['public']);
        $handACard = $game->rule->createCardDesc($values['playerA']);
        $handBCard = $game->rule->createCardDesc($values['playerB']);
        
        $cardsA = $game->rule->calcWeight($handACard, $publicCards);
        $cardsB = $game->rule->calcWeight($handBCard, $publicCards);
        
        $this->assertEquals($cardsA->getRanking(), 'two_pair');
        $this->assertEquals($cardsB->getRanking(), 'two_pair');
        
        $this->assertTrue($cardsA->getWeight() < $cardsB->getWeight(), $cardsA->getWeight().' < '.$cardsB->getWeight());
    }
    
    public function testRankingWeightStraight1()
    {   
        $game = $this->_createGame();
        $values = array(
            'public'    =>  '3/hearts, 4/hearts, 7/clubs, 10/spades, jack/diamonds',
            'playerA'   =>  '5/clubs, 6/hearts',
            'playerB'   =>  '9/spades, 8/hearts'//winner
        );
        $publicCards = $game->rule->createCardDesc($values['public']);
        $handACard = $game->rule->createCardDesc($values['playerA']);
        $handBCard = $game->rule->createCardDesc($values['playerB']);
        
        $cardsA = $game->rule->calcWeight($handACard, $publicCards);
        $cardsB = $game->rule->calcWeight($handBCard, $publicCards);
        
        $this->assertEquals($cardsA->getRanking(), 'straight');
        $this->assertEquals($cardsB->getRanking(), 'straight');
        
        $this->assertTrue($cardsA->getWeight() < $cardsB->getWeight(), $cardsA->getWeight().' < '.$cardsB->getWeight());
    }
    
     /*public function testRankingWeightFlush1() //FAILURES!
    {   
        $game = $this->_createGame();
        $values = array(
            'public'    =>  'king/diamonds, 4/diamonds, 5/diamonds, 4/spades, 7/clubs',
            'playerA'   =>  '6/diamonds, 8/diamonds',
            'playerB'   =>  '7/diamonds, 10/diamonds'//winner
        );
        $publicCards = $game->rule->createCardDesc($values['public']);
        $handACard = $game->rule->createCardDesc($values['playerA']);
        $handBCard = $game->rule->createCardDesc($values['playerB']);
        
        $cardsA = $game->rule->calcWeight($handACard, $publicCards);
        $cardsB = $game->rule->calcWeight($handBCard, $publicCards);
        
        $this->assertEquals($cardsA->getRanking(), 'Flush');
        $this->assertEquals($cardsB->getRanking(), 'Flush');
        
        $this->assertTrue($cardsA->getWeight() < $cardsB->getWeight(), $cardsA->getWeight().' < '.$cardsB->getWeight());
    }*/
    
    public function testRankingWeightHighCard() 
    {   
        $game = $this->_createGame();
        $values = array(
            'public'    =>  'ace/diamonds, king/hearts, 9/spades, 7/clubs, 5/diamonds',
            'playerA'   =>  '2/diamonds, 10/clubs',
            'playerB'   =>  '2/clubs, jack/hearts'//winner
        );
        $publicCards = $game->rule->createCardDesc($values['public']);
        $handACard = $game->rule->createCardDesc($values['playerA']);
        $handBCard = $game->rule->createCardDesc($values['playerB']);
        
        $cardsA = $game->rule->calcWeight($handACard, $publicCards);
        $cardsB = $game->rule->calcWeight($handBCard, $publicCards);
        
        $this->assertEquals($cardsA->getRanking(), 'high_card');
        $this->assertEquals($cardsB->getRanking(), 'high_card');
        
        $this->assertTrue($cardsA->getWeight() < $cardsB->getWeight(), $cardsA->getWeight().' < '.$cardsB->getWeight());
    }
    
      public function testRankingWeightHighCard1() 
    {   
        $game = $this->_createGame();
        $values = array(
            'public'    =>  '2/diamonds, 4/hearts, 8/spades, 5/clubs, 7/clubs',
            'playerA'   =>  '3/spades, jack/hears',
            'playerB'   =>  'king/diamonds, 6/clubs'//winner
        );
        $publicCards = $game->rule->createCardDesc($values['public']);
        $handACard = $game->rule->createCardDesc($values['playerA']);
        $handBCard = $game->rule->createCardDesc($values['playerB']);
        
        $cardsA = $game->rule->calcWeight($handACard, $publicCards);
        $cardsB = $game->rule->calcWeight($handBCard, $publicCards);
        
        $this->assertEquals($cardsA->getRanking(), 'high_card');
        $this->assertEquals($cardsB->getRanking(), 'straight');
        
        $this->assertTrue($cardsA->getWeight() < $cardsB->getWeight(), $cardsA->getWeight().' < '.$cardsB->getWeight());
    }
    
    public function testBank(){
        
        $game = $this->_createGame();
        $game->setOptions(array(
            'blinds'    =>  array(1, 2)
        ));
        
        $player1 = $game->createPlayer(array(
            'id'        =>  '1000',
            'username'  =>  'Микола Миколайович'
        ), 997);
        $game->join($player1, 1);
        
        $player2 = $game->createPlayer(array(
            'id'        =>  '2000',
            'username'  =>  'Володимир Коновалюк'
        ), 5003);
        $game->join($player2, 2);
        
        $player3 = $game->createPlayer(array(
            'id'        =>  '3000',
            'username'  =>  'Любомир Ковальов'
        ), 9998);
        $game->join($player3, 3);
        
        $player4 = $game->createPlayer(array(
            'id'        =>  '4000',
            'username'  =>  'Ігор Васильович'
        ), 369);
        $game->join($player4, 4);
        
        $player6 = $game->createPlayer(array(
            'id'        =>  '6000',
            'username'  =>  'Andriy Sayak'
        ), 183);
        $game->join($player6, 6);
        
        $player7 = $game->createPlayer(array(
            'id'        =>  '7000',
            'username'  =>  'Vasya Doutchak'
        ), 393);
        $game->join($player7, 7);
        
        $player8 = $game->createPlayer(array(
            'id'        =>  '8000',
            'username'  =>  'Jon Jozi'
        ), 2008);
        $game->join($player8, 8);
        
        $player9 = $game->createPlayer(array(
            'id'        =>  '9000',
            'username'  =>  'Andriy Igorovich'
        ), 1998);
        $game->join($player9, 9);
        
        $player10 = $game->createPlayer(array(
            'id'        =>  '10000',
            'username'  =>  'Андрій Васильович'
        ), 3423);
        $game->join($player10, 10);
        
        
        $game->dealerPosition = 1;
        $game->rule->setTestWeight(array(
            'weights'    =>  array(
                1   =>  61828,
                2   =>  61633,
                3   =>  61373,
                4   =>  32708,
                6   =>  60736,
                7   =>  59826,
                8   =>  61828,
                9   =>  32526,
                10  =>  32318
            )
        ));
        $game->start();
        
        
        $game->call($player6);
        $game->call($player7);
        $game->call($player8);
        $game->call($player9);
        $game->call($player10);
        $game->call($player1);
        $game->call($player2);
        $game->call($player3);
        
        $game->raise($player4, 3);
        $game->call($player6);
        $game->call($player7);
        $game->call($player8);
        $game->call($player9);
        $game->call($player10);
        
        $game->raise($player1, 4);
        $game->call($player2);
        $game->call($player3);
        $game->call($player4);
        $game->allin($player6);
        
        $game->call($player7);
        $game->call($player8);
        $game->call($player9);
        $game->allin($player10);
        $game->allin($player1);
        $game->allin($player2);
        $game->allin($player3);
        $game->allin($player4);
        $game->allin($player7);
        $game->allin($player8);
        $game->call($player9);
        
        $this->assertTrue($game->isGameEnd);
    }
    
    public function testLeave(){
        
        $game = $this->_createGame();
        $game->setOptions(array(
            'blinds'    =>  array(1, 2)
        ));
        $player1 = $game->createPlayer(array(
            'id'        =>  '1000',
            'username'  =>  'Любомир Ковальов'
        ), 10594);
        $game->join($player1, 1);
        $player2 = $game->createPlayer(array(
            'id'        =>  '22000',
            'username'  =>  'Андрій Васильович '
        ), 41335);
        $game->join($player2, 2);
        
        $player3 = $game->createPlayer(array(
            'id'        =>  '33000',
            'username'  =>  'Andriy Sayak'
        ), 84704);
        $game->join($player3, 9);
        $game->dealerPosition = 2;
        $game->start();
        
        $game->call($player3);
        $game->leave($player1, 'close');
        $game->check($player2);
        
        $game->check($player3);
        $game->check($player2);
        
        $game->check($player3);
        $game->leave($player2, 'close');
        
        $this->assertTrue($game->isGameEnd);
    }
    
    public function testLiyf3(){
        
        $game = $this->_createGame();
        $game->setOptions(array(
            'blinds'    =>  array(1, 2)
        ));
        $player1 = $game->createPlayer(array(
            'id'        =>  '1000',
            'username'  =>  'Любомир Ковальов'
        ), 10594);
        $game->join($player1, 1);
        $player2 = $game->createPlayer(array(
            'id'        =>  '22000',
            'username'  =>  'Андрій Васильович '
        ), 41335);
        $game->join($player2, 2);
        
        $player3 = $game->createPlayer(array(
            'id'        =>  '33000',
            'username'  =>  'Andriy Sayak'
        ), 84704);
        $game->join($player3, 9);
        $game->dealerPosition = 2;
        $game->start();
        
        $game->call($player3);
        $game->allin($player1);
        $game->call($player2);
        $game->raise($player3, 17735);
        $game->call($player2);
    }

    public function testAllInAndLastTimeout(){
        $game = $this->_createGame();
        $game->setOptions(array(
            'blinds'    =>  array(1, 2)
        ));
        $player1 = $game->createPlayer(array(
            'id'        =>  1000,
            'username'  =>  'Андрій Васильович'
        ), 3198);
        $game->join($player1, 1);
        $player2 = $game->createPlayer(array(
            'id'        =>  22000,
            'username'  =>  'Anna Zonova'
        ), 5550129);
        $game->join($player2, 3);
        
        $player3 = $game->createPlayer(array(
            'id'        =>  33000,
            'username'  =>  'Andriy Sayak'
        ), 1000);
        $game->join($player3, 8);
        
        $player4= $game->createPlayer(array(
            'id'        =>  35000,
            'username'  =>  'Любомир Ковальов'
        ), 2000);
        $game->join($player4, 9);
        $game->dealerPosition = 3;
        $game->rule->setTestWeight(array(
            'weights'    =>  array(
                1   =>  81029,
                3   =>  79976,
                8   =>  8102,
                9   =>  195065
            )
        ));
        $game->start();
        $game->call($player2);
        $game->allin($player3);
        $game->allin($player4);
        $game->allin($player1);
        $game->timeout($player2);
        $banks = $game->getBanks();
        for($i=0;$i<count($banks);$i++){
            $this->assertTrue(isset($banks[$i]));
        }
        $this->assertTrue($game->isGameEnd);
    }
    
    public function testDrawAndAllIn(){
        $game = $this->_createGame();
        $game->setOptions(array(
            'blinds'    =>  array(1, 2)
        ));
        $player1 = $game->createPlayer(array(
            'id'        =>  1000,
            'username'  =>  'Андрій Васильович'
        ), 3198);
        $game->join($player1, 1);
        $player2 = $game->createPlayer(array(
            'id'        =>  22000,
            'username'  =>  'Anna Zonova'
        ), 5550129);
        $game->join($player2, 3);
        
        $player3 = $game->createPlayer(array(
            'id'        =>  33000,
            'username'  =>  'Andriy Sayak'
        ), 1000);
        $game->join($player3, 8);
        
        $player4= $game->createPlayer(array(
            'id'        =>  35000,
            'username'  =>  'Любомир Ковальов'
        ), 2000);
        $game->join($player4, 9);
        $game->dealerPosition = 3;
        $game->rule->setTestWeight(array(
            'weights'    =>  array(
                1   =>  81029,
                3   =>  79976,
                8   =>  81029,
                9   =>  195065
            )
        ));
        $game->start();
        $game->call($player2);
        $game->allin($player3);
        $game->allin($player4);
        $game->allin($player1);
        $game->allin($player2);
        $banks = $game->getBanks();
        for($i=0;$i<count($banks);$i++){
            $this->assertTrue(isset($banks[$i]));
        }
        $this->assertTrue($game->isGameEnd);
    }
    
    public function testBalance(){
        $game = $this->_createGame();
        $game->setOptions(array(
            'blinds'    =>  array(1, 2)
        ));
        $player1 = $game->createPlayer(array(
            'id'        =>  1000,
            'username'  =>  'Player1'
        ), 10000);
        $game->join($player1, 10);
        $player2 = $game->createPlayer(array(
            'id'        =>  22000,
            'username'  =>  'Player2'
        ), 90000);
        $game->join($player2, 3);
        
        $player3 = $game->createPlayer(array(
            'id'        =>  33000,
            'username'  =>  'Player3'
        ), 9000);
        $game->join($player3, 6);
        $game->dealerPosition = 10;
        
        $game->start();
        $game->rule->setTestWeight(array(
            'weights'    =>  array(
                6   =>  1000,
                10  =>  0
            )
        ));
        $game->call($player2);
        $game->allin($player3);
        $game->call($player1);
        $game->call($player2);
        
        $game->allin($player1);
        $this->assertEquals(0, $player1->getBalance());
        
        $game->timeout($player2);
        $this->assertTrue($game->isGameEnd);
        $this->assertEquals(1000, $player1->getBalance());
        $this->assertEquals(27000, $player3->getBalance());
        
    }
    
    public function testBalance2(){
        $game = $this->_createGame();
        $game->setOptions(array(
            'blinds'    =>  array(1, 2)
        ));
        $player1 = $game->createPlayer(array(
            'id'        =>  1000,
            'username'  =>  'Player1'
        ), 10000);
        $game->join($player1, 10);
        $player2 = $game->createPlayer(array(
            'id'        =>  22000,
            'username'  =>  'Player2'
        ), 90000);
        $game->join($player2, 3);
        
        $player3 = $game->createPlayer(array(
            'id'        =>  33000,
            'username'  =>  'Player3'
        ), 9000);
        $game->join($player3, 6);
        $game->dealerPosition = 10;
        
        $game->start();
        $game->rule->setTestWeight(array(
            'weights'    =>  array(
                6   =>  0,
                10  =>  1000
            )
        ));
        $game->call($player2);
        $game->allin($player3);
        $game->call($player1);
        $game->call($player2);
        
        $game->allin($player1);
        $game->timeout($player2);
        
        $this->assertTrue($game->isGameEnd);
        $this->assertEquals(28000, $player1->getBalance());
        $this->assertEquals(81000, $player2->getBalance());
        $this->assertEquals(0, $player3->getBalance());
    }
    
    public function testLiyfBug2(){
        $game = $this->_createGame();
        $game->setOptions(array(
            'blinds'    =>  array(1, 2)
        ));
        $player1 = $game->createPlayer(array(
            'id'        =>  1000,
            'username'  =>  'Player1'
        ), 10000);
        $game->join($player1, 10);
        $player2 = $game->createPlayer(array(
            'id'        =>  22000,
            'username'  =>  'Player2'
        ), 900000);
        $game->join($player2, 3);
        $game->dealerPosition = 10;
        
        $game->start();
        
        $game->call($player1);
        
        $game->allin($player2);
        $game->call($player1);
    }
    
    public function testLiyfBug(){
        $game = $this->_createGame();
        $game->setOptions(array(
            'blinds'    =>  array(1, 2)
        ));
        $player1 = $game->createPlayer(array(
            'id'        =>  1,
            'username'  =>  'Player1'
        ), 1000);
        $game->join($player1, 1);
        $player2 = $game->createPlayer(array(
            'id'        =>  2,
            'username'  =>  'Player2'
        ), 1500);
        $game->join($player2, 2);
        $player3 = $game->createPlayer(array(
            'id'        =>  3,
            'username'  =>  'Player3'
        ), 503);
        $game->dealerPosition = 2;
        $game->join($player3, 10);
        
        $game->start();
        
        $game->call($player3);
        $game->call($player1);
        $game->check($player2);
        $this->assertEquals(1, $game->step);
        
        $game->allin($player3);
        
        $game->call($player1);
        $game->raise($player2, 577);
        $game->call($player1);
        
        $this->assertEquals(2, $game->step);
        
        $game->check($player2);
        $game->check($player1);
        
        $this->assertEquals(3, $game->step);
        
        $game->check($player2);
        $game->check($player1);
        
        $this->assertTrue($game->isGameEnd);
    }
    
    
    public function testAllInAnotherStep(){
        $game = $this->_createGame();
        $player1 = $game->createPlayer(array(
            'id'        =>  1,
            'username'  =>  'Player1'
        ), 500);
        $game->join($player1, 1);
        $player2 = $game->createPlayer(array(
            'id'        =>  2,
            'username'  =>  'Player2'
        ), 2000);
        $game->join($player2, 2);
        $player3 = $game->createPlayer(array(
            'id'        =>  3,
            'username'  =>  'Player3'
        ), 3000);
        $game->join($player3, 3);
        $player4 = $game->createPlayer(array(
            'id'        =>  4,
            'username'  =>  'Player4'
        ), 4000);
        $game->join($player4, 4);
        $game->start();
        
        $game->allin($player4);
        $this->assertEquals(3000, $game->bank->getCallAllSum($player4->info['id']));
        
        $game->allin($player1);
        $this->assertEquals(500, $game->bank->getCallAllSum($player1->info['id']));
        
        $game->allin($player2);
        $this->assertEquals(2000, $game->bank->getCallAllSum($player2->info['id']));
        
        $game->allin($player3);
        $this->assertEquals(3000, $game->bank->getCallAllSum($player3->info['id']));
        
        $this->assertEquals(8500, $game->bank->getBalance());
    }
    
    public function testAllInIfLowBank(){
        $game = $this->_createGame();
        $player1 = $game->createPlayer(array(
            'id'        =>  1,
            'username'  =>  'Player1'
        ), 500);
        $game->join($player1, 1);
        $player2 = $game->createPlayer(array(
            'id'        =>  2,
            'username'  =>  'Player2'
        ), 2000);
        $game->join($player2, 2);
        $game->start();
        $game->allin($player2);
        $this->assertEquals(500, $game->bank->getCallAllSum($player2->info['id']));
        $game->call($player1);
        $this->assertEquals(500, $game->bank->getCallAllSum($player1->info['id']));
    }
    
    public function testNotUseRaiseIfAllIn(){
        $game = $this->_createGame();
        $player1 = $game->createPlayer(array(
            'id'        =>  1,
            'username'  =>  'Player1'
        ), 500);
        $game->join($player1, 1);
        $player2 = $game->createPlayer(array(
            'id'        =>  2,
            'username'  =>  'Player2'
        ), 1000);
        $game->join($player2, 2);
        
        $player3 = $game->createPlayer(array(
            'id'        =>  3,
            'username'  =>  'Player3'
        ), 4000);
        $game->join($player3, 3);
        $game->start();
        $game->allin($player1);
        $this->assertEquals(500, $game->bank->getCallAllSum($player1->info['id']));
        
        $game->raise($player2, 800);
        $this->assertEquals(800, $game->bank->getCallAllSum($player2->info['id']));
        
        $error = false;
        try{
            $game->raise($player3, 1001);
        } catch (ExceptionRule $e) {
            $error = $e->getMessage();
        }
        $this->assertEquals($error, 'big raise');

        $error = false;
        try{
            $game->raise($player3, 10000);
        } catch (ExceptionRule $e) {
            $error = $e->getMessage();
        }
        $this->assertEquals($error, 'no money');
        
        $game->call($player3);
        $game->check($player2);
        
        $game->allin($player3);
        $this->assertEquals(1000, $game->bank->getCallAllSum($player3->info['id']));
        $game->call($player2);
    }
    
    public function testAllInIfBigBank_ALias_allin(){
        $game = $this->_createGame();
        $player1 = $game->createPlayer(array(
            'id'        =>  1,
            'username'  =>  'Player1'
        ), 5000);
        $game->join($player1, 1);
        $player2 = $game->createPlayer(array(
            'id'        =>  2,
            'username'  =>  'Player2'
        ), 2000);
        $game->join($player2, 2);
        $game->start();
        $game->allin($player2);
        $this->assertEquals(2000, $game->bank->getCallAllSum($player2->info['id']));
        $game->allin($player1);
        $this->assertEquals(2000, $game->bank->getCallAllSum($player1->info['id']));
    }
    
    public function testAllInIfBigBank(){
        $game = $this->_createGame();
        $player1 = $game->createPlayer(array(
            'id'        =>  1,
            'username'  =>  'Player1'
        ), 5000);
        $game->join($player1, 1);
        $player2 = $game->createPlayer(array(
            'id'        =>  2,
            'username'  =>  'Player2'
        ), 2000);
        $game->join($player2, 2);
        $game->start();
        $game->allin($player2);
        $this->assertEquals(2000, $game->bank->getCallAllSum($player2->info['id']));
        $game->call($player1);
        $this->assertEquals(2000, $game->bank->getCallAllSum($player1->info['id']));
    }
    
    public function testNextDealer(){
        $game = $this->_createGame();
        $player1 = $game->createPlayer(array(
            'id'        =>  1,
            'username'  =>  'Player1'
        ), 500);
        $game->join($player1, 1);
        $player2 = $game->createPlayer(array(
            'id'        =>  2,
            'username'  =>  'Player2'
        ), 2000);
        $game->join($player2, 2);
        $game->start();
        $this->assertEquals(1, $game->dealerPosition);
        $game->fold($player1);
        $game->start();
        $this->assertEquals(2, $game->dealerPosition);
        $game->fold($player2);
        $game->start();
        $this->assertEquals(1, $game->dealerPosition);
    }
    
    public function testNotPlayGame()
    {
        $game = $this->_createGame();
        $player1 = $game->createPlayer(array(
            'id'        =>  1,
            'username'  =>  'Player1'
        ), 500);
        $game->join($player1, 1);
        $player2 = $game->createPlayer(array(
            'id'        =>  2,
            'username'  =>  'Player2'
        ), 2000);
        $game->join($player2, 2);
        
        $player3 = $game->createPlayer(array(
            'id'        =>  3,
            'username'  =>  'Player3'
        ), 1000);
        $game->start();
        $game->join($player3, 3);
        $game->allin($player2);
        $game->call($player1);
        $this->assertTrue($game->isGameEnd);
    }
    
    public function testErrorsStarEndGame()
    {
        $game = $this->_createGame();
        $player1 = $game->createPlayer(array(
            'id'        =>  1,
            'username'  =>  'Player1'
        ), 500);
        $game->join($player1, 1);
        $player2 = $game->createPlayer(array(
            'id'        =>  2,
            'username'  =>  'Player2'
        ), 2000);
        $game->join($player2, 2);
        
        $player3 = $game->createPlayer(array(
            'id'        =>  3,
            'username'  =>  'Player3'
        ), 1000);
        
        $player4 = $game->createPlayer(array(
            'id'        =>  4,
            'username'  =>  'Player4'
        ), 1000);
        $game->join($player4, 4);
        
        $error = false;
        try{
            $game->join($player4, 10);
        } catch (ExceptionRule $e) {
            $error = $e->getMessage();
        }
        $this->assertEquals($error, 'player in game');
        
        $error = false;
        try{
            $player5 = $game->createPlayer(array(
                'id'        =>  5,
                'username'  =>  'Player5'
            ), 1000);
            $game->join($player5, 4);
        } catch (ExceptionRule $e) {
            $error = $e->getMessage();
        }
        $this->assertEquals($error, 'position is busy');
        
        $this->assertEquals('Wait firsts players', $game->status());
        $error = false;
        try{
            $game->timeout($player1);
        } catch (ExceptionRule $e) {
            $error = $e->getMessage();
        }
        $this->assertEquals($error, 'game not active');
        
        $error = false;
        try{
            $game->check($player1);
        } catch (ExceptionRule $e) {
            $error = $e->getMessage();
        }
        $this->assertEquals($error, 'game not active');
        
        $error = false;
        try{
            $game->raise($player1, 0);
        } catch (ExceptionRule $e) {
            $error = $e->getMessage();
        }
        $this->assertEquals($error, 'game not active');
        
        $error = false;
        try{
            $game->call($player1);
        } catch (ExceptionRule $e) {
            $error = $e->getMessage();
        }
        $this->assertEquals($error, 'game not active');
        
        $error = false;
        try{
            $game->fold($player1);
        } catch (ExceptionRule $e) {
            $error = $e->getMessage();
        }
        $this->assertEquals($error, 'game not active');
        
        $this->assertTrue($game->isGameEnd);
        
        $game->start();
        
        $game->join($player3, 3);
        
        $error = false;
        try{
            $game->fold($player3);
        } catch (ExceptionRule $e) {
            $error = $e->getMessage();
        }
        $this->assertEquals($error, 'you not play');
        
        $error = false;
        try{
            $game->call($player3);
        } catch (ExceptionRule $e) {
            $error = $e->getMessage();
        }
        $this->assertEquals($error, 'you not play');
        
        $error = false;
        try{
            $game->check($player3);
        } catch (ExceptionRule $e) {
            $error = $e->getMessage();
        }
        $this->assertEquals($error, 'you not play');
        
        $this->assertNotEquals('Wait firsts players', $game->status());
        $this->assertFalse($game->isGameEnd);
        
        $error = false;
        try{
            $game->start();
        } catch (ExceptionRule $e) {
            $error = $e->getMessage();
        }
        $this->assertEquals($error, 'game is active');
        
        $game->fold($player1);
        $error = false;
        try{
            $game->fold($player1);
        } catch (ExceptionRule $e) {
            $error = $e->getMessage();
        }
        $this->assertEquals($error, 'you already is fold');
        $game->fold($player4);
        
        $this->assertTrue($game->isGameEnd);
    }
    
    public function testTimeoutAndEndGame()
    {
        $game = $this->_createGame();
        $player1 = $game->createPlayer(array(
            'id'        =>  1,
            'username'  =>  'Player1'
        ), 500);
        $game->join($player1, 1);
        $player2 = $game->createPlayer(array(
            'id'        =>  2,
            'username'  =>  'Player2'
        ), 2000);
        $game->join($player2, 2);
        
        $player3 = $game->createPlayer(array(
            'id'        =>  3,
            'username'  =>  'Player3'
        ), 1000);
        $game->join($player3, 3);

        $game->start();
        $game->timeout($player1);
        $game->timeout($player2);
        $this->assertTrue($game->isGameEnd);
    }
    
    public function testTimeoutAndTurn()
    {
        $game = $this->_createGame();
        $player1 = $game->createPlayer(array(
            'id'        =>  1,
            'username'  =>  'Player1'
        ), 500);
        $game->join($player1, 1);
        $player2 = $game->createPlayer(array(
            'id'        =>  2,
            'username'  =>  'Player2'
        ), 2000);
        $game->join($player2, 2);
        
        $player3 = $game->createPlayer(array(
            'id'        =>  3,
            'username'  =>  'Player3'
        ), 1000);
        $game->join($player3, 3);

        $game->start();
        $game->timeout($player1);
        $this->assertEquals(2, $game->turnPosition);
    }
    
    public function testAllReturnCall2Win()
    {
        $game = $this->_createGame();
        $player1 = $game->createPlayer(array(
            'id'        =>  1,
            'username'  =>  'Player1'
        ), 500);
        $game->join($player1, 1);
        $player2 = $game->createPlayer(array(
            'id'        =>  2,
            'username'  =>  'Player2'
        ), 1000);
        $game->join($player2, 2);
        
        $player3 = $game->createPlayer(array(
            'id'        =>  3,
            'username'  =>  'Player3'
        ), 4000);
        $game->join($player3, 3);

        $game->start();
        
        $game->call($player1);
        $game->call($player2);
        $game->allin($player3);
        $game->call($player1);
        $game->call($player2);
        $this->assertTrue($game->isGameEnd);
    }
    
    public function testAllReturnCallWin()
    {
        $game = $this->_createGame();
        $player1 = $game->createPlayer(array(
            'id'        =>  1,
            'username'  =>  'Player1'
        ), 4000);
        $game->join($player1, 1);
        $player2 = $game->createPlayer(array(
            'id'        =>  2,
            'username'  =>  'Player2'
        ), 1000);
        $game->join($player2, 2);

        $game->start();
        
        $game->allin($player2);
        $game->call($player1);
        $this->assertTrue($game->isGameEnd);
    }
    
    public function testRaiseMinWin()
    {
        $game = $this->_createGame();
        $player1 = $game->createPlayer(array(
            'id'        =>  1,
            'username'  =>  'Player1'
        ), 100);
        $game->join($player1, 1);
        $player2 = $game->createPlayer(array(
            'id'        =>  2,
            'username'  =>  'Player2'
        ), 100);
        $game->join($player2, 2);
        $player3 = $game->createPlayer(array(
            'id'        =>  3,
            'username'  =>  'Player3'
        ), 100);
        $game->join($player3, 3);

        $game->start();
        
        $this->assertEquals($game->bank->getCallAllSum($player3->info['id']), 10);
        $this->assertEquals($game->bank->getCallAllSum($player2->info['id']), 5);
        
        $this->assertEquals($game->getMinRaise(), 10);
        
        $game->call($player1);
        $game->raise($player2, 30);
        $this->assertEquals($game->bank->getCallAllSum($player2->info['id']), 30);
        $this->assertEquals($game->getMinRaise(), 30);
    }
    
    public function testRaiseMaxIfAllInWin()
    {
        $game = $this->_createGame();
        $player1 = $game->createPlayer(array(
            'id'        =>  1,
            'username'  =>  'Player1'
        ), 100);
        $game->join($player1, 1);
        $player2 = $game->createPlayer(array(
            'id'        =>  2,
            'username'  =>  'Player2'
        ), 100);
        $game->join($player2, 2);

        $game->start();
        
        $this->assertEquals($game->bank->getCallAllSum($player1->info['id']), 10);
        $this->assertEquals($game->bank->getCallAllSum($player2->info['id']), 5);
        
        $game->call($player2);
        $game->raise($player1, 20);
        $this->assertEquals($game->bank->getCallAllSum($player1->info['id']), 20);
        $game->raise($player2, 99);
        $game->call($player1);
        $this->assertEquals($game->bank->getCallAllSum($player1->info['id']), 99);
        $this->assertEquals($game->bank->getCallAllSum($player2->info['id']), 99);
        
        $this->assertEquals($game->step, 1);
        
        $game->check($player2);
        $game->check($player1);
        
        $this->assertEquals($game->step, 2);
        
        $game->check($player2);
        $game->check($player1);
        
        $this->assertEquals($game->step, 3);
        
        $game->check($player2);
        $game->check($player1);
        $this->assertTrue($game->isGameEnd);
    }
    
    public function testRaiseAndCallWin()
    {
        $game = $this->_createGame();
        $player1 = $game->createPlayer(array(
            'id'        =>  1,
            'username'  =>  'Player1'
        ), 100);
        $game->join($player1, 1);
        $player2 = $game->createPlayer(array(
            'id'        =>  2,
            'username'  =>  'Player2'
        ), 1000);
        $game->join($player2, 2);

        $game->start();
        
        $game->call($player2);
        $game->raise($player1, 20);
        $game->call($player2);
        $this->assertEquals($game->step, 1);
        $game->raise($player1, 20);
        $game->call($player2);
        $this->assertEquals($game->step, 2);
        $game->raise($player1, 20);
        $game->call($player2);
        $this->assertEquals($game->step, 3);
        $game->raise($player1, 20);
        $game->call($player2);
        $this->assertTrue($game->isGameEnd);
    }
    
    public function testTurnWin()
    {
        $game = $this->_createGame();
        $players = array();
        for($i = 1; $i<=10;$i++){
            $players[$i] = $game->createPlayer(array(
                'id'        =>  $i,
                'username'  =>  'Player'.$i
            ), 100);
            $game->join($players[$i], $i);
        }
        $game->start();
        echo $game->turnPosition."\n";
        $start = $game->turnPosition;
        for($i = 0; $i<9;$i++){
            $pos = $start+$i;
            $pos = ($pos>10)?($pos)%10:$pos;
            $game->call($players[$pos]);
        }
        for($i = 9; $i<10;$i++){
            $pos = $start+$i;
            $pos = ($pos>10)?($pos)%10:$pos;
            $game->check($players[$pos]);
        }
        
        $this->assertEquals($game->step, 1);
        for($i = 0; $i<10;$i++){
            $pos = $start+$i;
            $pos = ($pos>10)?($pos)%10:$pos;
            $game->check($players[$pos]);
        }
        $this->assertEquals($game->step, 2);
        for($i = 0; $i<10;$i++){
            $pos = $start+$i;
            $pos = ($pos>10)?($pos)%10:$pos;
            $game->check($players[$pos]);
        }
        $this->assertEquals($game->step, 3);
        for($i = 0; $i<10;$i++){
            $pos = $start+$i;
            $pos = ($pos>10)?($pos)%10:$pos;
            $game->check($players[$pos]);
        }
    }
    
    public function testMaxPlayerWin()
    {
        $game = $this->_createGame();
        $players = array();
        for($i = 1; $i<=11;$i++){
            $players[$i] = $game->createPlayer(array(
                'id'        =>  $i,
                'username'  =>  'Player'.$i
            ), 100);
            $game->join($players[$i], $i);
        }
        $error = false;
        try{
            $game->start();
        } catch (\Game\Model\Poker\Exception\ExceptionRule $e) {
            $error = $e->getMessage();
        }
        $this->assertTrue($game->isGameEnd);
        $this->assertEquals($error, 'not have normal count players');
    }
    
    public function testMinPlayerWin()
    {
        $game = $this->_createGame();
        $error = false;
        try{
            $game->start();
        } catch (\Game\Model\Poker\Exception\ExceptionRule $e) {
            $error = $e->getMessage();
        }
        $this->assertTrue($game->isGameEnd);
        $this->assertEquals($error, 'not have normal count players');
    }
    
    public function testAllInAndRaiseLeft1StepWin()
    {
        $game = $this->_createGame();
        $player1 = $game->createPlayer(array(
            'id'        =>  1,
            'username'  =>  'Player1'
        ), 100);
        $game->join($player1, 1);
        $player2 = $game->createPlayer(array(
            'id'        =>  2,
            'username'  =>  'Player2'
        ), 1000);
        $game->join($player2, 2);

        $game->start();
        
        $game->call($player2);
        $game->allin($player1);
        $game->call($player2);
        $this->assertTrue($game->isGameEnd);
    }
    
    public function testAllInAndRaiseRight1StepWin()
    {
        $game = $this->_createGame();
        $player1 = $game->createPlayer(array(
            'id'        =>  1,
            'username'  =>  'Player1'
        ), 100);
        $game->join($player1, 1);
        $player2 = $game->createPlayer(array(
            'id'        =>  2,
            'username'  =>  'Player2'
        ), 1000);
        $game->join($player2, 2);

        $game->start();
        
        $game->allin($player2);
        $game->call($player1);
        $this->assertTrue($game->isGameEnd);
    }
    
    public function testAllInAndRaise2StepWin()
    {
        $game = $this->_createGame();
        $playerA = $game->createPlayer(array(
            'id'        =>  1,
            'username'  =>  'PlayerA'
        ), 100);
        $game->join($playerA, 1);
        $playerB = $game->createPlayer(array(
            'id'        =>  2,
            'username'  =>  'PlayerB'
        ), 1000);
        $game->join($playerB, 2);

        $game->start();
        
        $game->call($playerB);
        $game->check($playerA);
        
        $this->assertEquals($game->getOption('public_cards'), $game->publicCards->count());
        
        $game->raise($playerB, 90);
        $game->call($playerA);
        $this->assertTrue($game->isGameEnd);
    }
    
    public function testAllIn1Win()
    {
        $game = $this->_createGame();
        $playerA = $game->createPlayer(array(
            'id'        =>  1,
            'username'  =>  'Player1'
        ), 100);
        $game->join($playerA, 1);
        $playerB = $game->createPlayer(array(
            'id'        =>  2,
            'username'  =>  'Player2'
        ), 100);
        $game->join($playerB, 2);
        $playerC = $game->createPlayer(array(
            'id'        =>  3,
            'username'  =>  'Player3'
        ), 1000);
        $game->join($playerC, 3);

        $game->start();
        $game->call($playerA);
        $game->call($playerB);
        $game->check($playerC);
        $this->assertEquals($game->getOption('public_cards'), $game->publicCards->count());
        $game->allin($playerA);
        $game->fold($playerB);
        $game->call($playerC);
        
        $banks = $game->getBanks();
        $this->assertEquals(1, count($banks));
        $this->assertTrue($game->isGameEnd);
    }
    
    public function testAllIn2Win()
    {
        $game = $this->_createGame();
        $playerA = $game->createPlayer(array(
            'id'        =>  1,
            'username'  =>  'Player1'
        ), 1000);
        $game->join($playerA, 1);
        $playerB = $game->createPlayer(array(
            'id'        =>  2,
            'username'  =>  'Player2'
        ), 100);
        $game->join($playerB, 2);

        $game->start();
        
        $game->call($playerB);
        $game->check($playerA);
        $this->assertEquals($game->getOption('public_cards'), $game->publicCards->count());
        $game->allin($playerB);
        $game->call($playerA);
        $banks = $game->getBanks();
        $this->assertEquals(1, count($banks));
        $this->assertTrue($game->isGameEnd);
    }
    
    public function testAll()
    {
        return;
        $game = $this->_createGame();
        $players = array();
        $playerCount = 5;
        for($i=1;$i<=$playerCount;$i++){
            $player = $game->createPlayer(array(
                'id'        =>  $i,
                'username'  =>  'Player'.$i
            ), 1000);
            $game->join($player, $i);
            $players[] = $player;
        }
        $game->start();
        foreach($game->getPlayers() AS $player){
            if($player->isPlay()){
                $this->assertEquals($player->cards->count(), $game->getOption('hand_cards'));
            }
        }
        $run = function($game){
            while($game->isGameEnd === false){
                while($game->isStepEnd === false){
                    if(rand(1,5) == 1){
                        
                            $game->allin($game->getTurnPlayer());
                    }elseif(rand(1,30) == 1){
                        $game->fold($game->getTurnPlayer());
                    }else{
                        try {
                            $game->call($game->getTurnPlayer());
                        } catch (\Game\Model\Poker\Exception\ExceptionRule $exc) {
                            $game->check($game->getTurnPlayer());
                        }
                    }
                    if($game->step == 1 && $game->getTurnPlayer()->info['username']=='Player2'){
                        try {
                            $player = $game->createPlayer(array(
                                'id'        =>  6,
                                'username'  =>  'Player6'
                            ), 1000);
                            $game->join($player, 6);
                        } catch (\Game\Model\Poker\Exception\ExceptionRule $exc) {
                        }
                    }
                }
            }
        };
        $run($game);
        $this->assertEquals($game->publicCards->count(), $game->getOption('public_cards_max'), 'test me');
        $game->end();
        $this->assertEquals($game->step, $game->getOption('rounds'));
        
    }

    protected function _createGame()
    {
        $game = new \Game\Model\Poker\Game(array(
            'manual_mode'   =>  true
        ));
        $logger = new \Zend\Log\Logger;
        $logger->addWriter('stream', null, array('stream' => 'php://output'));
        $game->setLogger($logger);
        $logger->info(' ----------- init new game ----------- ');
        return $game;
    }
}
