<?php

namespace Ap\Provider;

use Zend\ServiceManager\ServiceManager;
use Ap\Model\Rowset;

trait ProvidesServiceManager {

    /**
     * @var ServiceManager
     */
    //public $_sm;

    /**
     * Retrieve service manager instance
     *
     * @return ServiceManager
     */
    public function getSm() 
    {
        return $this->_sm;
    }

    /**
     * Set service manager instance
     *
     * @param ServiceManager $locator
     * @return User
     */
    public function setSm(ServiceManager $serviceManager) 
    {
        $this->_sm = $serviceManager;
        $vars = get_object_vars($this);
        foreach ($vars AS $value) {
            if ($value instanceof Rowset) {
                $value->setSm($serviceManager);
            }
        }
        return $this;
    }
}
