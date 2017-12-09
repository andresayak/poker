<?php

namespace Ap\Model;

class Exception  extends \Exception
{
    public function __construct ($message, $code=null, $previous=null) 
    {
        echo $message;exit;
    }
}