<?php

namespace Game\Model\User;

use Zend\View\Model\ViewModel;
use Zend\Mail\Message;

class Mailer {

    public static function forgot(Row $userRow, $sm) 
    {
        $mailer = $sm->get('Mailer');
        $viewModel = new ViewModel(array(
            'userRow'   =>  $userRow,
            'site_name' =>  $mailer->getOption('site_name')
        ));
        $viewModel->setTemplate('email/user/forgot.phtml');

        $message = new Message();
        $message->addTo($userRow->email)
            ->setSubject('Recovery password');

        $mailer->send($viewModel, $message);
    }
    
    public static function signup(Row $userRow, $sm) 
    {
        $mailer = $sm->get('Mailer');
        $viewModel = new ViewModel(array(
            'userRow'   =>  $userRow,
            'site_name' =>  $mailer->getOption('site_name')
        ));
        $viewModel->setTemplate('email/user/signup.phtml');

        $message = new Message();
        $message->addTo($userRow->email)
            ->setSubject('Sign up');

        $mailer->send($viewModel, $message);
    }
    
    public static function changeemail(Row $userRow, $sm)
    {
        $mailer = $sm->get('Mailer');
        $viewModel = new ViewModel(array(
            'userRow'=>$userRow,
            'site_name' =>  $mailer->getOption('site_name')
        ));

        $message = new Message();
        $message->addTo($userRow->email_new)
            ->setSubject('Confirm email');
        $viewModel->setTemplate('email/user/changeemail_confirm.phtml');
        $mailer->send($viewModel, $message);
        
        $message = new Message();
        $message->addTo($userRow->email)
            ->setSubject('Notification: Confirm email');
        $viewModel->setTemplate('email/user/changeemail_notification.phtml');
        $mailer->send($viewModel, $message);
    }
}
