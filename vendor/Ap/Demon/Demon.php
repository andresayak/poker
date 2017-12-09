<?php

namespace Ap\Demon;

class Demon extends AbstractDemon {

    protected $some_delay = 3600;

    public function work($command) 
    {
        $path = realpath(dirname(INDEX_PATH).'/..').'/';
        $timeStart = microtime(true);
        $command = 'php '.$path.$command;
        exec($command . ' > /dev/null 2>&1 &');
        $time = microtime(true) - $timeStart;
        //sleep(1);
        if ($time < 1){
            usleep((1 - $time) * 1000000);
        }
    }
}