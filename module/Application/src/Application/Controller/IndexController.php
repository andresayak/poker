<?php

namespace Application\Controller;

use Ap\Controller\AbstractController;

class IndexController extends AbstractController
{
    public function indexAction()
    {
    }
    
    public function appAction()
    {
        if(AUTH_TYPE != 'fb'){
            echo 'AUTH_TYPE = '.AUTH_TYPE;
            exit;
        }
        $config = $this->getServiceLocator()->get('config');
        $this->layout('layout/app');
        $this->getResponse()->setContent('');
        $app_id = $config['facebook']['app_id'];
        $url = $config['facebook']['url'];
        return array(
            'appOptions'    =>  array(
                'ver'=> APP_VERSION,
                'auth'=> AUTH_TYPE,
                'time'=> time(),
                'work_underway' =>  array(
                    'status'        =>  ($config['workStatus']['status'] && $config['workStatus']['workStart'] && $config['workStatus']['workEnd']),
                    'time_start'    =>  $config['workStatus']['workStart'],
                    'time_end'      =>  $config['workStatus']['workEnd']
                ),
                'app_id'    =>  $app_id,
                'app_url'   =>  $url
            ),
        );
    }
}
