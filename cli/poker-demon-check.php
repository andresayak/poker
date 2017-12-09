<?php

ini_set('display_startup_errors', 1);
ini_set('display_errors',1);
ini_set('error_reporting', E_ALL & ~E_DEPRECATED);
date_default_timezone_set('UTC');

define('INDEX_PATH', realpath(dirname(__FILE__).'/../'));
chdir(INDEX_PATH);

date_default_timezone_set('UTC');
$config = array_merge(
    include 'config/autoload/global.php',
    include 'config/autoload/local.php'
);
$server_code = $config['server_code'];
$dbconfig = $config['multiServers']['dbslave'];
if(!$server_code){
    echo '$server_code not set'."\n";
    exit;
}
try {
    
    $dbh = new PDO($dbconfig['dsn'], $dbconfig['username'], $dbconfig['password'], array(PDO::ATTR_PERSISTENT => false));

    $time = time();

    $stmt = $dbh->prepare('SELECT id FROM poker WHERE server_code = "'.$server_code.'" AND status = 1 AND time_update IS NOT NULL AND time_update <= ' . $time);

    $stmt->execute();
    
    $pokers_ids = array();
    while ($rs = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $pokers_ids[] = $rs['id'];
    }
    $pokers_ids = array_unique($pokers_ids);
    foreach ($pokers_ids AS $poker_id) {
        $command = 'php public/index.php poker timeout --id=' . $poker_id;
        echo $command."\n";
        exec($command . ' > /dev/null 2>&1 &');
    }
    
    $stmt = $dbh->prepare('SELECT id FROM poker WHERE server_code = "'.$server_code.'" AND status = 0 AND time_start IS NOT NULL AND time_start <= ' . $time.' AND max_players>1');

    $stmt->execute();
    
    $pokers_ids = array();
    while ($rs = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $pokers_ids[] = $rs['id'];
    }
    $pokers_ids = array_unique($pokers_ids);
    foreach ($pokers_ids AS $poker_id) {
        $command = 'php public/index.php poker start --id=' . $poker_id;
        echo $command."\n";
        exec($command . ' > /dev/null 2>&1 &');
    }
    echo "Done\n";
    
} catch (PDOException $e) {
    print "Error!: " . $e->getMessage() . "<br/>";
    die();
}