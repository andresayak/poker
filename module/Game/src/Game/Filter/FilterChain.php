<?php

namespace Game\Filter;

class FilterChain extends \Zend\Filter\FilterChain
{
    protected $_filter;
    protected $invokableClasses = array(
        'Lang'      =>  'Game\Filter\Lang',
        'wallSlot'  =>  'Game\Filter\WallSlot',
    );
    
    public function __construct($options = null) 
    {
        parent::__construct($options);
        foreach($this->invokableClasses AS $name=>$path){
            $this->getPluginManager()->setInvokableClass($name, $path);
        }
    }
    
    public function plugin($name, array $options = null)
    {
        $plugins = $this->getPluginManager();
        $filter = $plugins->get($name, $options);
        if($filter instanceof AbstractFilter){
            $filter->setFilter($this->getFilter());
        }
        return $filter;
    }
    public function attachByName($name, $options = array(), $priority = self::DEFAULT_PRIORITY)
    {
        if (!is_array($options)) {
            $options = (array) $options;
        } elseif (empty($options)) {
            $options = null;
        }
        $filter = $this->getPluginManager()->get($name, $options);
        if($filter instanceof AbstractFilter){
            $filter->setFilter($this->getFilter());
        }
        return $this->attach($filter, $priority);
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