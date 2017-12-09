<?php

namespace Ap\Form;

use Zend\Form\FormInterface;
use Zend\ServiceManager\ServiceManager;

class Form extends \Zend\Form\Form
{
    protected $_sm;
    
    public function setObject($object)
    {
        parent::setObject($object);
        if($object instanceof \Ap\Model\Row){
            $key = $object->getTable()->getKey();
            if(!$object->isNotSave() && $this->has($key)){
                $this->get($key)->setAttribute('disabled', 'disabled');
                $object->getInputFilter()->remove($key);
            }
        }
        return $this;
    }
    
    public function getSm()
    {
        return $this->_sm;
    }
    
    public function setSm(ServiceManager $serviceManager)
    {
        $this->_sm = $serviceManager;
        return $this;
    }
}