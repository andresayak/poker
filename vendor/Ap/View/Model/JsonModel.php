<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Ap\View\Model;

use Traversable;
use Zend\Json\Json;
use Zend\Stdlib\ArrayUtils;

class JsonModel extends \Zend\View\Model\JsonModel
{
    protected $_options;
    public function setJsonOptions($options)
    {
        $this->_options = $options;
        return $this->_options;
    }
    public function serialize()
    {
        $variables = $this->getVariables();
        if ($variables instanceof Traversable) {
            $variables = ArrayUtils::iteratorToArray($variables);
        }

        if (null !== $this->jsonpCallback) {
            return $this->jsonpCallback.'('.Json::encode($variables).');';
        }
        return json_encode($variables, $this->_options);
    }
}
