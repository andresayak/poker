<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Ap\Paginator\Adapter;

class DbSelect extends \Zend\Paginator\Adapter\DbSelect
{
    public function getItems($offset, $itemCountPerPage)
    {
        $select = clone $this->select;
        $select->offset($offset);
        $select->limit($itemCountPerPage);

        $statement = $this->sql->prepareStatementForSqlObject($select);
        $result    = $statement->execute();

        $resultSet = clone $this->resultSetPrototype;
        $resultSet->initialize($result);

        return $resultSet;
    }

}
