<?php

function scanRecursive($baseDir, &$list = array(), $folder = false)
{
    $files = scandir($baseDir);
    foreach($files AS $file){
        if($file!='..' and $file!= '.'){
            $filename = $baseDir.'/'.$file;
            $path = (($folder)?$folder.'/':'').$file;
            if(is_dir($filename)){
                scanRecursive($filename, $list, $path);
            }else{
                if(is_file($filename) and preg_match('/\.js$/', $filename))
                    $list[] = str_replace ('.js', '', $path);
            }
        }
    }
    return $list;
}

$appDir = 'public/js/app';
$outFile = 'public/js/build.js';
$files = scanRecursive($appDir);
$str = "({
    baseUrl: 'app',
    out: 'build/main.js',
    optimize: 'uglify2',
    name: 'main',
    include: ['".  implode('\',\'', $files)."'],
    exclude: ['config/local'],
    wrap: true,
    paths: {
        jquery: '../lib/jquery-1.11.1.min',
        hammer: '../lib/hammer',
        'jquery.cookie': '../lib/jquery.cookie',
        'jquery.emoticons': '../lib/jquery.emoticons',
        'jquery.hammer': '../lib/jquery.hammer',
        'facebook': '//connect.facebook.net/en_US/all',
        'mailru': '//connect.mail.ru/js/loader',
        'vkontakte': '//vk.com/js/api/xd_connection',
        'jquery.mb': '../lib/jquery.mb.audio',
        'odnoklassniki': '//api.odnoklassniki.ru/js/fapi5'
    }
})";

file_put_contents($outFile, $str);
echo $str;