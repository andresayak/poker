<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Ap\Db\Sql;

use Zend\Db\Sql\Sql;

class Sql extends Sql
{
    protected $_for_update = false;
    
    public function setForUpdate($status)
    {
        $this->_for_update = $status;
        return $this;
    }
    
    public function getForUpdate()
    {
        return $this->_for_update;
    }
}
