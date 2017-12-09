<?php

namespace Control\Controller;

use Ap\Controller\AbstractController;

class DemonController extends AbstractController
{
    public function pokerAction()
    {
        $mode = $this->params()->fromRoute('mode');
        $demon = new \Ap\Demon\Demon('globalamuse_poker_daemon');
        $demon->setSm($this->getServiceLocator());
        if($mode == 'start'){
            if ( $demon->isDaemonActive() )
                die('Daemon active'."\n");
            echo 'Demon run'."\n";
            $demon->execute('cli/poker-demon-check.php');
        }elseif($mode == 'stop'){
            echo 'Demon stop'."\n";
            $demon->stop();
        }
    }
}
