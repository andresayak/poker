<?php

namespace Game\Model\Lib\Notification;

use Ap\Model\Table as Prototype;

class Table extends Prototype
{
    protected $_name = 'lib_notification';
    protected $_key =  'code';
    protected $_cols = array(
        'code', 'title', 'filename'
    );
}