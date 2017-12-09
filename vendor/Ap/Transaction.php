<?php

namespace Ap;

use Zend\InputFilter\InputFilterInterface;

class Transaction 
{
    protected $_sm, $_dbs;
    protected $_names = array();
    protected $_input_filter;
    protected $_callback;
    protected $_status = false;
    protected $_log = array();
    
    public function __construct($sm) 
    {
        $this->_sm = $sm;
        return $this;
    }
    
    public function getSm()
    {
        return $this->_sm;
    }
    
    public function request($data, $globalDb = false)
    {
        $statusEnd = false;
        $atten = 0;
        $attenMax = 10;
        $lastmessage = '';
        while(!$statusEnd){
            try{
                $this->start($globalDb);
                $filter = $this->getInputFilter();
                $filter->setData($data);
                $filter->setValidationGroup(InputFilterInterface::VALIDATE_ALL);
                if($filter->isValid()){
                    $callback = $this->getCallback();
                    $result = true;
                    if($callback){
                        $callback($filter);
                    }else{ 
                        if($filter->finish() === false){
                            $result = false;
                        }
                    }
                }else $result = false;
                $this->end($globalDb);
                $statusEnd = true;
            /*} catch (\Ap\MongoDb\ConcurentException $e){
                $this->rollback($globalDb);
                $statusEnd = false;*/
            } catch (\Zend\Db\Adapter\Exception\InvalidQueryException $e){
                $this->rollback($globalDb);
                $statusEnd = false;
                $lastmessage = $e->getMessage();
            } catch (\Exception $e) {
                $this->rollback($globalDb);
                $statusEnd = false;
                throw new \Exception('Invalid transaction Exception('.$lastmessage.')', 0, $e);
            }
            if(!$statusEnd){
                $atten++;
                if($atten > $attenMax){
                    throw new \Exception('Invalid transaction (attend = '.$atten.', error '.$e->getMessage().')', 0, $e);
                }
            }
        }
        return $this->_status = $result;
    }
    
    public function run($action)
    {
        $statusEnd = false;
        $atten = 0;
        $lastmessage = '';
        $attenMax = 10;
        while(!$statusEnd){
            try{
                $this->start();
                $action();
                $this->end();
                $statusEnd = true;
            } catch (\Zend\Db\Adapter\Exception\InvalidQueryException $e){
                $this->rollback();
                $statusEnd = false;
                $lastmessage = $e->getMessage();
            } catch (\Exception $e) {
                $this->rollback();
                $statusEnd = false;
                throw new \Exception('Invalid transaction Exception('.$e->getMessage().')', 0, $e);
            }
            if(!$statusEnd){
                $atten++;
                if($atten > $attenMax){
                    throw new \Exception('Invalid transaction (attend = '.$atten.', error '.$lastmessage.')');
                }
            }
        }
        $this->_dbs = array();
        return $this;
    }
    
    public function setCallback($func)
    {
        $this->_callback = $func;
        return $this;
    }
    
    public function getCallback()
    {
        return $this->_callback;
    }
    
    public function setInputFilter($filter)
    {
        $this->_input_filter = $filter;
        return $this;
    }
    
    public function getInputFilter()
    {
        return $this->_input_filter;
    }
    
    public function setDbs($params)
    {
        $this->_dbs = array();
        foreach($params['MultiServers'] AS $name){
            $this->_dbs[] = $this->_sm->get('MultiServers\Service')->getAdapter($name);
        }
        return $this;
    }
    public function getDbs()
    {
        if(!$this->_dbs || !count($this->_dbs)){
            throw new \Exception('databases not set');
        }
        return $this->_dbs;
    }
    
    public function start()
    {
        foreach($this->getDbs() AS $db){
            $connection = $db->getDriver()->getConnection();
            $connection->beginTransaction();
        }
        return $this;
    }
    
    public function end()
    {
        foreach($this->getDbs() AS $db){
            $connection = $db->getDriver()->getConnection();
            $connection->commit();
        }
        return $this;
    }
    
    public function rollback()
    {
        foreach($this->getDbs() AS $db){
            $connection = $db->getDriver()->getConnection();
            if($connection->isConnected()){
                $connection->rollback();
            }
        }
        return $this;
    }
    
    public function log($message)
    {
        $this->_log[] = $message;
        return $this;
    }
    
    public function isSuccess()
    {
        return $this->_status;
    }
}