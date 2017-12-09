<?php

namespace Ap\Validator;

use Zend\Stdlib\PriorityQueue;

class ValidatorChain extends \Zend\Validator\ValidatorChain
{
    protected $_results, $_sm, $_filter;
    protected $invokableClasses = array(
        'passwordHash'          =>  'Ap\Validator\PasswordHash',
    );
    
    public function __construct() 
    {
        $this->validators = new PriorityQueue();
        
        foreach($this->invokableClasses AS $name=>$path){
            $this->getPluginManager()->setInvokableClass($name, $path);
        }
    }
    
    public function plugin($name, array $options = null)
    {
        $plugins = $this->getPluginManager();
        $validator = $plugins->get($name, $options);
        if($validator instanceof AbstractValidator){
            $validator->setFilter($this->getFilter());
        }
        return $validator;
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
}
