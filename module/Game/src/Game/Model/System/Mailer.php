<?php

namespace Game\Model\System;

use Zend\View\Model\ViewModel;
use Zend\Mail\Message;

class Mailer {

    public static function feedback(Feedback\Row $feedbackRow, $sm) 
    {
        $config = $sm->get('config');
        $mailer = $sm->get('Mailer');
        $viewModel = new ViewModel(array(
            'feedbackRow'   =>  $feedbackRow,
            'servername'    =>  $config['server']['name']
        ));
        $viewModel->setTemplate('email/system/feedback.phtml');

        $emails = $mailer->getOption('feedback_emails');
        $message = new Message();
        $message->setSubject('Feedback');

        foreach($emails AS $email){
            $message->addTo($email);
        }
        $message->addReplyTo($feedbackRow->email);
        $mailer->send($viewModel, $message);
    }
}
