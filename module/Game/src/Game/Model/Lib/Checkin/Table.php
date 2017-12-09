<?php

namespace Game\Model\Lib\Checkin;

use Ap\Model\Table as Prototype;

class Table extends Prototype
{
    use Game\Model\Lib\Resource\Provider;
    
    protected $_name = 'lib_checkin';
    
    protected $_cols = array(
        'id', 'period', 'resource_code', 'count'
    );
}