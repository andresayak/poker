<?php

namespace Ap\InputFilter;

use Ap\Validator\ValidatorChain;
use Ap\Filter\FilterChain;

class InputFilter extends \Zend\InputFilter\InputFilter
{
    protected $_logs = array();
    protected $_sm;
    
    public function getFactory()
    {
        if (null === $this->factory) {
            parent::getFactory();
            $validatorChain = new ValidatorChain;
            $validatorChain->setFilter($this);
            
            $filterChain = new FilterChain;
            $filterChain->setFilter($this);
            
            $this->getFactory()->setDefaultValidatorChain($validatorChain);
            $this->getFactory()->setDefaultFilterChain($filterChain);
        }
        return $this->factory;
    }
    
    public function setSm($sm)
    {
        $this->_sm = $sm;
        return $this;
    }
    
    public function getSm()
    {
        return $this->_sm;
    }
    
    public function log($message)
    {
        $this->_logs[] = $message;
        return $this;
    }
    
    public function isLogs()
    {
        return count($this->_logs);
    }
    
    public function getLogs($sep = "\n")
    {
        return implode($sep, $this->_logs);
    }
}