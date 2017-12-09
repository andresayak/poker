<?php

namespace Api\Controller;

use Api\Controller\AbstractController;
use Zend\View\Model\JsonModel;
use Zend\View\Model\ViewModel;
use Zend\View\Renderer\PhpRenderer;
use Zend\View\Resolver;
use Game\InputFilter;

class UserController extends AbstractController
{
    public function playAction()
    {
        $request = $this->getRequest();
        if ($request->isPost()) {
            $data = $request->getPost()->toArray();
        }else{
            $data = $this->params()->fromQuery();
        }
        
        if(AUTH_TYPE == 'fb'){
            $filter = new InputFilter\User\PlayFacebook($this->getServiceLocator());
        }else{
            return $this->outError('type invalid');
        }
        $transaction = $this->getServiceLocator()->get('Transaction');
        $transaction->setDbs(array(
            'MultiServers'  =>  array('master')
        ));
        $transaction->setInputFilter($filter);
        if ($transaction->request($data)) {
            $out = array(
                'signkey'       =>  $filter->getUidRow()->signkey,
                'access_token'  =>  $filter->getUidRow()->token,
                'user_row'      =>  $filter->getUserRow()->toArrayForApi()
            );
            return $this->outOk($out);
        }
        return $this->outError($filter->getMessages());
    }
    
    public function homeAction()
    {
        $authUser = $this->getAuthUser();
        $service = $this->getServiceLocator()->get('Chat\Service');
        $allianceMessages = array();
        if($authUser->isMemberRow()){
            $allianceMessages = array();//$service->getByAllianceId($authUser->alliance_id)->toArrayForApi();
        }
        $allMessages = array();//$service->getPublic()->toArrayForApi();
        return $this->outOk(array(
            'library'  =>  $this->getServiceLocator()->get('libraryData'),
            'userdata'  =>  $this->getServiceLocator()->get('userData'),
            'chatlists' => array(
                'public'    =>  $allMessages,
                'alliance'  =>  $allianceMessages
            )
        ));
    }
}