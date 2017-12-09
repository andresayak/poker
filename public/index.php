<?php 
ini_set('display_errors', 1);
ini_set('error_reporting', E_ALL & ~E_DEPRECATED);

$timeLimit = (PHP_SAPI == 'cli') ? 0 : 60;
set_time_limit($timeLimit);
ini_set('max_execution_time', $timeLimit * 2);
date_default_timezone_set('UTC');

define('REQUEST_MICROTIME', microtime(true));
define('INDEX_PATH', __FILE__);
define('IP_ADDRESS', get_client_ip());

set_error_handler('exceptions_error_handler');
register_shutdown_function('fatal_handler');

function get_client_ip() {
    $ipaddress = '';
    if (isset($_SERVER['HTTP_CLIENT_IP']))
        $ipaddress = $_SERVER['HTTP_CLIENT_IP'];
    else if (isset($_SERVER['HTTP_X_FORWARDED_FOR']))
        $ipaddress = $_SERVER['HTTP_X_FORWARDED_FOR'];
    else if (isset($_SERVER['HTTP_X_FORWARDED']))
        $ipaddress = $_SERVER['HTTP_X_FORWARDED'];
    else if (isset($_SERVER['HTTP_FORWARDED_FOR']))
        $ipaddress = $_SERVER['HTTP_FORWARDED_FOR'];
    else if (isset($_SERVER['HTTP_FORWARDED']))
        $ipaddress = $_SERVER['HTTP_FORWARDED'];
    else if (isset($_SERVER['REMOTE_ADDR']))
        $ipaddress = $_SERVER['REMOTE_ADDR'];
    else
        $ipaddress = false;
    return $ipaddress;
}

function fatal_handler() {
    $error = error_get_last();
    if ($error !== null) {
        $errno = $error["type"];
        $errfile = $error["file"];
        $errline = $error["line"];
        $errstr = $error["message"];
        $message = print_r($error, true);
        $hash = crc32(md5($message . date('Y-m-d-H')));
        $file = dirname(INDEX_PATH) . '/../data/tmp/' . date('Y-m-d-H') . '_' . $hash;
        if (!is_file($file)) {
            /* if (defined('ERROR_MAILTO') and defined('SERVER_ID') and ERROR_MAILTO) {
              mail(ERROR_MAILTO, 'Fatal error server #' . SERVER_ID . ' hash ' . $hash, print_r($error, true)."\n"
              .'SERVER: '. ((isset($_SERVER))?print_r($_SERVER, true):'')."\n"
              .'REQUEST: '. ((isset($_REQUEST))?print_r($_REQUEST, true):'')."\n");
              } */
            file_put_contents($file, $message);
        }
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode(array(
                'exception' =>  true,
                'httpStatus'    =>  500,
                'title'         =>  'Fatal error',
                'message'       =>  $message,
                'error_id'      =>  $hash,
            ));
        exit;
    }
}

// Setup error handler
function exceptions_error_handler($severity, $message, $filename, $lineno) {
    if (error_reporting() == 0) {
        return;
    }
    if (error_reporting() & $severity) {
        throw new \ErrorException($message, 0, $severity, $filename, $lineno);
    }
}
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

chdir(dirname(__DIR__));

// Setup autoloading
require 'init_autoloader.php';

// Run the application!
$app = Zend\Mvc\Application::init(require 'config/application.config.php');
$app->run();