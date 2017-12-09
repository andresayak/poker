<?php

namespace GameTest;

use GameTest\Bootstrap;
use Zend\Mvc\Router\Http\TreeRouteStack as HttpRouter;
use Game\Controller\IndexController;
use Zend\Http\Request;
use Zend\Http\Response;
use Zend\Mvc\MvcEvent;
use Zend\Mvc\Router\RouteMatch;
use PHPUnit_Framework_TestCase;

abstract class ControllerTest extends \Zend\Test\PHPUnit\Controller\AbstractHttpControllerTestCase
{
    public function setUp()
    {
        $this->setApplicationConfig(
            include realpath(__DIR__ . '/../TestConfig.php')
        );
        $this->_sm = Bootstrap::getServiceManager();
        parent::setUp();
    }
    
    public function tearDown()
    {
        parent::tearDown();
        $this->reset();
    }

    public function auth()
    {
        $user_id = 38831;
        $userRow = $this->getSm()->get('User\Table')->fetchBy('id', $user_id);
        $authService = $this->getSm()->get('Auth\Service');
        $authService->setUserRow($userRow);
    }
    
    protected function getSm()
    {
        if($this->_sm === NULL){
            $this->setApplicationConfig(
                include realpath(__DIR__ . '/../TestConfig.php')
            );
            $this->_sm = Bootstrap::getServiceManager();
        }
        return $this->_sm;
    }
    
    
    protected function getTable($name)
    {
        return $this->getSm()->get($name.'/Table');
    }
}