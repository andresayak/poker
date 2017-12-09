<?php

namespace Ap\Demon;

abstract class AbstractDemon {

    CONST MEMLIMIT   = 512;
    protected $pid_file_name      = '/tmp/my_pid_file.pid';
    protected $child_processes    = array();
    protected static $stop_server = false;
    protected $some_delay         = 30;
    protected $_count = 10;
    protected $_sm;
    
    public function __construct($count=1) 
    {
        $this->_count = $count;
        //Без этой директивы PHP не будет перехватывать сигналы
        //declare(ticks=1);
        //регистрируем обработчик
        //@pcntl_signal(SIGTERM, array('Demon',"sig_handler"));
    }

    public function setSm($sm)
    {
        $this->_sm = $sm;
    }
    
    public function getSm()
    {
        return $this->_sm;
    }
    
    public function stop()
    {
        if(is_file($this->pid_file_name)){
            $pid = file_get_contents($this->pid_file_name);
            unlink($this->pid_file_name);
            if ($pid) {
                exec('kill ' . $pid);
            }
        }
        
    }
    
    public function execute($city_id = null) 
    {
        $child_pid = pcntl_fork();
        if( $child_pid ) {
            file_put_contents($this->pid_file_name, $child_pid);
            echo 'exit from parent proccess'.exit;
            exit;
        }

        //posix_setsid();
        
        while (!self::$stop_server) {
            $this->work($city_id);
            if(round(memory_get_peak_usage()/1048576, 3) > self::MEMLIMIT){
                echo 'memory not remain ('.memory_get_peak_usage().' > '.self::MEMLIMIT.' MB)'."\n";
                unlink($this->pid_file_name);
                exit;
            }
        }
    }

    abstract protected function work($command);

    //Обработчик
    public static function sigHandler($signo) 
    {return;
        switch($signo) {
            case SIGTERM: {
                self::$stop_server = true;
                break;
            }
            default: {
            }
        }
    }

    public function isDaemonActive() 
    {
        if( is_file($this->pid_file_name) ) {
            $pid = file_get_contents($this->pid_file_name);
            if(posix_kill($pid,0)) {
                return true;
            } else {
                if(!unlink($this->pid_file_name)) {
                    exit(-1);
                }
            }
        }
        return false;
    }

    /**
     * test method
     */
    public function test() {
        $this->work();
    }

}