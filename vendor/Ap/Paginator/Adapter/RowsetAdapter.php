<?php

namespace Ap\Paginator\Adapter;

use Zend\Paginator\Adapter\AdapterInterface;

class RowsetAdapter implements AdapterInterface
{
    /**
     * ArrayAdapter
     *
     * @var array
     */
    protected $rowset = null;

    /**
     * Item count
     *
     * @var int
     */
    protected $count = null;

    /**
     * Constructor.
     *
     * @param array $array ArrayAdapter to paginate
     */
    public function __construct($rowset, $count=false)
    {
        $this->rowset = $rowset;
        $this->count = ($count)?$count:$rowset->getCount();
    }

    /**
     * Returns an array of items for a page.
     *
     * @param  int $offset Page offset
     * @param  int $itemCountPerPage Number of items per page
     * @return array
     */
    public function getItems($offset, $itemCountPerPage)
    {
        return $this->rowset;
    }

    /**
     * Returns the total number of rows in the array.
     *
     * @return int
     */
    public function count()
    {
        return $this->count;
    }
}
