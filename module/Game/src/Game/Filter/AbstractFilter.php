<?php

namespace Game\Filter;

 class AbstractFilter extends \Zend\Filter\AbstractFilter
{
    protected $_filter;
    
    public function getFilter()
    {
        return $this->_filter;
    }
    
    public function setFilter($filter)
    {
        $this->_filter = $filter;
        return $this;
    }
    
    public function filter($value){
        return $value;
    }
}