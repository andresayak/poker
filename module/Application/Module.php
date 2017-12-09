<?php

namespace Application;

use Zend\Mvc\ModuleRouteListener;
use Zend\Mvc\MvcEvent;
use Zend\Session\Container;
use Zend\Http\Request as HttpRequest;
use Zend\View\Model\JsonModel;
use Zend\View\Model\ModelInterface;

class Module
{
    public function onBootstrap(MvcEvent $e)
    {
        $e->getApplication()->getEventManager()->attach(MvcEvent::EVENT_RENDER, array($this, 'sendPush'), 200);
        $config = $e->getApplication()->getServiceManager()->get('config');
        
        if(isset($config['phpSettings'])){
            $phpSettings = $config['phpSettings'];
            if($phpSettings) {
                foreach($phpSettings as $key => $value) {
                    ini_set($key, $value);
                }
            }
        }
        if(isset($config['constants'])){
            foreach ($config['constants'] as $key => $value) {
                if (!defined($key)) {
                    define($key, $value);
                }
            }
        }
        
        $eventManager        = $e->getApplication()->getEventManager();
        $moduleRouteListener = new ModuleRouteListener();
        $moduleRouteListener->attach($eventManager);
        $this->bootstrapSession($e);
        $eventManager->attach('dispatch.error', function($event){
            $exception = $event->getResult()->exception;
            if ($exception) {
                $sm = $event->getApplication()->getServiceManager();
                $service = $sm->get('Application\Service\ErrorHandling');
                $service->logException($exception, $event->getApplication()->getServiceManager());
            }
            
        });
        $eventManager->attach(MvcEvent::EVENT_RENDER, array($this, 'onRenderError'));
    }
    
    public function sendPush($e)
    {
        $PushCommetService = $e->getApplication()->getServiceManager()->get('PushCommet\Service');
        $PushCommetService->run();
    }
    
    public function onRenderError($e)
    {
        if (!$e->isError()) {
            return;
        }

        $request = $e->getRequest();
        if (!$request instanceof HttpRequest) {
            return;
        }

        $headers = $request->getHeaders();
        if (!$headers->has('Accept')) {
            return;
        }

        $accept = $headers->get('Accept');
        $match  = $accept->match('application/json');
        if (!$match || $match->getTypeString() == '*/*') {
            return;
        }
        ini_set('html_errors', 0); 

        $currentModel = $e->getResult();
        if ($currentModel instanceof JsonModel) {
            return;
        }

        $response = $e->getResponse();
        $model = new JsonModel(array(
            'exception'     =>  true,
            'httpStatus'    =>  $response->getStatusCode(),
            'title'         =>  $response->getReasonPhrase(),
        ));

        $exception  = $currentModel->getVariable('exception');

        if ($currentModel instanceof ModelInterface && $currentModel->reason) {
            switch ($currentModel->reason) {
                case 'error-controller-cannot-dispatch':
                    $model->detail = 'The requested controller was unable to dispatch the request.';
                    break;
                case 'error-controller-not-found':
                    $model->detail = 'The requested controller could not be mapped to an existing controller class.';
                    break;
                case 'error-controller-invalid':
                    $model->detail = 'The requested controller was not dispatchable.';
                    break;
                case 'error-router-no-match':
                    $model->detail = 'The requested URL could not be matched by routing.';
                    break;
                default:
                    $model->detail = $currentModel->message;
                    break;
            }
        }

        if ($exception) {
            if ($exception->getCode()) {
                $e->getResponse()->setStatusCode($exception->getCode());
            }
            $model->detail = $exception->getMessage();

            $messages = array();
            while ($exception = $exception->getPrevious()) {
                $messages[] = "* " . $exception->getMessage();
            }
            if (count($messages)) {
                $exceptionString = implode("n", $messages);
                $model->messages = $exceptionString;
            }
        }

        $model->setTerminal(true);
        $e->setResult($model);
        $e->setViewModel($model);
    }
    
    public function bootstrapSession($e)
    {
        if($_SERVER['PHP_SELF'] != '/usr/bin/phpunit' and $_SERVER['PHP_SELF']!= '/usr/local/bin/phpunit'){
            $session = $e->getApplication()
                ->getServiceManager()
                ->get('Zend\Session\SessionManager');
            if(!$e->getRequest() instanceof \Zend\Console\Request 
                and $token = $e->getRequest()->getQuery('token', false)
            ){
                $session->setId($token);
            }
            $session->start();

            $container = new Container('initialized');
            if (!isset($container->init)) {
                 //$session->regenerateId(true);
                 $container->init = 1;
            }
        }
    }
    
    public function getConfig()
    {
        return include __DIR__ . '/config/module.config.php';
    }

    public function getAutoloaderConfig()
    {
        return array(
            'Zend\Loader\StandardAutoloader' => array(
                'namespaces' => array(
                    __NAMESPACE__ => __DIR__ . '/src/' . __NAMESPACE__,
                ),
            ),
        );
    }
}
