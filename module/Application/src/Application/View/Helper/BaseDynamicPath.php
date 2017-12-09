<?php

namespace Application\View\Helper;

use Zend\View\Helper\AbstractHelper;

class BaseDynamicPath extends AbstractHelper {

    protected $_sm;

    public function __construct($sm) {
        $this->_sm = $sm;
        return $this;
    }

    public function getSm() {
        return $this->_sm;
    }

    public function __invoke() {
        $helper = $this->getSm()->get('basePath');
        $host = $helper();
        return 'http' . ((isset($_SERVER['HTTPS']) and $_SERVER['HTTPS'] == 'on') ? 's' : '') . preg_replace('/^https?:/', ':', $host);
    }

}
