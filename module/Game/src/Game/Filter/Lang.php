<?php

namespace Game\Filter;

class Lang extends AbstractFilter
{
    protected $options = array(
        'default'       =>  '',
        'type'          =>  'default',
        'availables'    =>  array()
    );
    public function setFilter($filter)
    {
        parent::setFilter($filter);
        $config = $this->getFilter()->getSm()->get('config');
        if(!isset($config['langs'])){
            throw new \Exception('langs list not set');
        }
        $this->setOptions($config['langs']);
        return $this;
    }
    
    
    public function filter($value)
    {
        if($this->options['type'] == 'fb'){
            $value = preg_replace('/^([^_]*_)/', '', $this->getFilter()->getFacebookLang());
        }
        $value = strtolower($value);
        if(in_array($value, $this->options['availables'])){
            return $value;
        }
        return $this->options['default'];
    }
}