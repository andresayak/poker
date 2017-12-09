<?php

function round_up($number, $precision = 2)
{
    $fig = (int) str_pad('1', $precision, '0');
    return (ceil($number * $fig) / $fig);
}

function round_down($number, $precision = 2)
{
    $fig = (int) str_pad('1', $precision, '0');
    return (floor($number * $fig) / $fig);
}