<?php

namespace Ap\Validator;

class AbstractValidator extends \Zend\Validator\AbstractValidator
{
    protected  $messageTemplates = array(
    );
    
    protected $_sm, $_filter;
    protected $options = array(
        'callback'         => null
    );
    
    public function getList()
    {
        return $this->messageTemplates;
    }
    
    public function __construct($options = null)
    {
        if (is_callable($options)) {
            $options = array('callback' => $options);
        }
        parent::__construct($options);
    }
    
    public function getCallback()
    {
        return $this->options['callback'];
    }
    
    public function setCallback($callback)
    {
        if (!is_callable($callback)) {
            throw new \Exception('Invalid callback given');
        }
        $this->options['callback'] = $callback;
        return $this;
    }
    
    public function getFilter()
    {
        return $this->_filter;
    }
    
    public function setFilter($filter)
    {
        $this->_filter = $filter;
        return $this;
    }
    
    public function setResult($value) 
    {
        if($this->getCallback()){
            call_user_func_array($this->getCallback(), array($value));
        }
    }
    
    public function isValid($value) {
    }
}