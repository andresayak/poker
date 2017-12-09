<?php

namespace Api\Controller;

use Zend\Mvc\MvcEvent,
    Zend\Mvc\Controller\AbstractActionController,
    Zend\Stdlib\RequestDescription as Request,
    Zend\Stdlib\ResponseDescription as Response;
use Zend\InputFilter\InputFilter;
use Zend\View\Model\JsonModel;
use Zend\Console\Request as ConsoleRequest;

class AbstractController extends AbstractActionController
{
    protected function attachDefaultListeners()
    {
        $events = $this->getEventManager();
        $events->attach(MvcEvent::EVENT_DISPATCH, array($this, 'checkToken'), 200);
        $events->attach(MvcEvent::EVENT_DISPATCH, array($this, 'checkAcl'), 100);
        parent::attachDefaultListeners();
    }
    
    public function checkToken(MvcEvent $e)
    {
        if($e->getRouteMatch() instanceof \Zend\Mvc\Router\Http\RouteMatch){
            if($token = $e->getRequest()->getQuery('access_token', false)){
                if(preg_match('/^[\w\d\-\,]*$/i', $token)){
                    session_id($token);
                }else{
                    echo 'invalid token';
                    exit;
                }
            }
        }
    }
    public function checkAcl(MvcEvent $e)
    {
        if($e->getRouteMatch() instanceof \Zend\Mvc\Router\Http\RouteMatch){
            $aclService = $this->getServiceLocator()->get('Acl\Service');
            $role = 'guest';
            $request = $e->getRequest();
            $authService = $this->getServiceLocator()->get('Auth\Service');
            if($authService->getUserRow()){
                $role = $authService->getUserRow()->role;
                /*if($authService->getUserRow()->isRemove() 
                    and $this->params('action') != 'remove' 
                    and $this->params('controller') != 'Application\Controller\Home\Setting'
                ){
                    return $this->redirect()->toRoute('home', array('controller'=>'setting', 'action'=>'remove'));
                }*/
            }
            $routeMatchName = $e->getRouteMatch()->getMatchedRouteName();
            if (!$aclService->isAllowed($role, $routeMatchName)) {
                $response = $this->getResponse();
                if($request->isXmlHttpRequest()){
                    $e->setViewModel(new JsonModel(array(array('error'=>'access denied'))));
                    $response->setStatusCode(401);
                }else{
                    $response->setStatusCode(401);
                    $e->getViewModel()->setTerminal(false);
                    /*$ViewModel = new \Zend\View\Model\ViewModel();
                    $ViewModel->setTerminal(true);
                    $e->setViewModel($ViewModel);*/
                    //$response->setContent('access denied');
                    echo 'access denied';exit;
                }
                $e->stopPropagation();  
            }
        }
    }
    
    public function out($data = array(), $paginator = null, $method = null)
    {
        $method = ($method === null)? 'toArrayForApi' : $method;
        if($paginator){
            $data['list'] = $paginator->getCurrentItems()->{$method}();
            $data['items_count'] = $paginator->getTotalItemCount();
            $data['page_count'] = count($paginator);
        }
        $data = $this->array_map_recursive($data);
        $data['time_now'] = time();
        if($this->params()->fromQuery('print', false)){
            echo '<pre>';
            print_r($data);
            echo '</pre>';
            exit;
        }
        if($profiler = $this->getServiceLocator()->get('Zend\Db\Adapter\Adapter')->getProfiler()){
            $time = 0;
            foreach($profiler->getProfiles() AS $row){
                $time+=$row['elapse'];
            }
            $data['profiler'] = array(
                'count' => count($profiler->getProfiles()), 
                'time' => $time, 
                'php-time'=>(microtime(true) - REQUEST_MICROTIME)
            );
            if($this->params()->fromQuery('profiler', false)){
                $data['profiler']['query'] = $profiler->getProfiles();
            }
        }
        $config = $this->getServiceLocator()->get('config');
        foreach($config['view_manager']['headers'] AS $headerName=>$headerValue){
            $this->getResponse()
                ->getHeaders()
                ->addHeaderLine($headerName, $headerValue);
        }
        return new JsonModel($data);
    }
    
    protected function array_map_recursive($arr) 
    {
        $rarr = array();
        foreach ($arr as $k => $v) {
            if (is_array($v)) {
                $rarr[$k] = $this->array_map_recursive($v);
            } else {
                if ($v === null) {
                    unset($rarr[$k]);
                }
                else
                    $rarr[$k] = $v;
            }
        }
        return $rarr;
    }
    
    public function outError($errorMsg)
    {
        $response = $this->getResponse();
        return $this->out(array(
            'error'     =>  $errorMsg,
            'status'    =>  'error'
        ));
    }
    
    public function outOk(array $options = array(), $paginator = null, $outMethod = null)
    {
        $data = array('status'  =>  true);
        $data = $data+$options;
        return $this->out($data, $paginator, $outMethod);
    }
    
    
    protected function _redirect($action = 'index', $query = array(), $route = null)
    {
        return $this->redirect()->toRoute($route, array(
            'controller'=>  $this->params('__CONTROLLER__'),
            'action'    =>  $action
        ), array(
            'query' =>  $query
        ));
    }
    
    protected function getRow($tableName, $key = false)
    {
        $table = $this->getTable($tableName);
        if(!$key){
            $key = $table->getKey();
        }
        if(!$value = $this->params()->fromQuery($key)
             or !$row = $table->fetchByPK($value)
        )
            return false;
        return $row;
    }
    
    protected function getTable($name)
    {
        return $this->getServiceLocator()->get($name.'\Table');
    }
    
    protected function getForm($name)
    {
        return $this->getServiceLocator()->get($name.'\Form');
    }
    
    public function getAuthUser()
    {
        return $this->getServiceLocator()->get('Auth\Service')->getUserRow();
    }
    
    public function addMessage($value, $type = 'info')
    {
        if($value instanceof InputFilter){
            foreach($value->getMessages() AS $input=>$messages){
                foreach($messages AS $message)
                    $this->addMessage($input.': '.$message, $type);
            }
            return $this;
        }
        if($translator = $this->getServiceLocator()->get('MvcTranslator')){
            $value = $translator->translate($value);
        }
        $namespace = $this->flashMessenger()->getNamespace();
        $this->flashMessenger()->setNamespace($type)
            ->addMessage($value)
            ->setNamespace($namespace);
    }
}