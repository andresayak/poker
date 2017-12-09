<?php

namespace Ap\Filter;

use Zend\Filter\FilterInterface;

class DateFormat implements FilterInterface
{
    public function filter($value, $format = '%m/%d/%Y')
    {
        $ts = strptime($value, $format);
        if($ts){
            $year = $ts['tm_year'] + 1900;
            $month = $ts['tm_mon'] + 1;
            $day = $ts['tm_mday'];

            $str= $year.'-'.$month.'-'.$day;
            return $str;
        }else{
            return $value;
        }
    }
}