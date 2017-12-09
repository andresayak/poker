<?php

namespace Api\Controller;

use Api\Controller\AbstractController;
use Zend\View\Model\JsonModel;
use Zend\View\Model\ViewModel;
use Zend\View\Renderer\PhpRenderer;
use Zend\View\Resolver;
use Game\InputFilter;

class ChatController extends AbstractController
{
    
    public function sayAction()
    {
        $query = $this->params()->fromQuery();
        if(!isset($query['type'])){
            return $this->outError('type not set');
        }elseif($query['type'] == 'public'){
            $filter = new InputFilter\Chat\All($this->getServiceLocator());
            $filter->setData($query);
            if ($filter->isValid()) {
                $filter->finish();
                return $this->outOk();
            }else{
                $data = $filter->getMessages();
                $response = $this->getResponse();
                $response->setStatusCode(200);
                $out = array(
                    'error'             =>  $data,
                    'status'            =>  false
                );
                if($filter->getUserRow()){
                    $out['ban_chat_status'] = $filter->getUserRow()->ban_chat_status;
                    $out['ban_chat_timeend'] = $filter->getUserRow()->ban_chat_timeend;
                }
                return $this->out($out);
            }
        }elseif($query['type'] == 'room'){
            $filter = new InputFilter\Chat\Room($this->getServiceLocator());
            $filter->setData($query);
            if ($filter->isValid()) {
                $filter->finish();
                return $this->outOk();
            }else{
                return $this->outError($filter->getMessages());
            }
        }
    }
    
    public function getListAction()
    {
        $service = $this->getServiceLocator()->get('Chat\Service');
        return $this->outOk(array(
            'list'  =>  $service->getPublic()->toArray(),
        ));
    }
}