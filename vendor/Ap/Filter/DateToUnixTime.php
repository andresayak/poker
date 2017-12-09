<?php

namespace Ap\Filter;

use Zend\Filter\FilterInterface;

class DateToUnixTime implements FilterInterface
{
    public function filter($value, $format = '%m/%d/%Y')
    {
        $ts = strptime($value, $format);
        if($ts){
            $year = $ts['tm_year'] + 1900;
            $month = $ts['tm_mon'] + 1;
            $day = $ts['tm_mday'];
            return mktime(0, 0, 0, $month, $day, $year);
        }else{
            return 0;
        }
    }
}