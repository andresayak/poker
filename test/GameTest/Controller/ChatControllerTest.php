<?php

namespace GameTest\Controller;

use GameTest\ControllerTest;
use Game\Model\Poker\Exception\ExceptionRule;
use Game\InputFilter;

class ChatControllerTest extends ControllerTest
{
    public function testList()
    {
        $this->auth();
        $this->dispatch('/api/chat.getList', 'GET');
        $this->assertNotRedirect();
        $json = $this->getResponse()->getBody();
        $this->assertJson($json, $json);
        $body = json_decode($json, 1);
        $this->assertArrayHasKey('list', $body);
        print_r($body);
    }
    
    public function testSayMessage()
    {
        $this->auth();
        
        $filter = new InputFilter\Chat\All($this->getSm());
        $filter->setData(array(
            'type'  =>  'public',
            'message'   =>  'test message'
        ));
        $this->assertTrue($filter->isValid(), print_r($filter->getMessages(), true));
        $filter->finish();
    }
}
