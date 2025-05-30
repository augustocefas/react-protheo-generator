<?php
    
    declare(strict_types=1);
    
    ini_set('display_errors', '1');
    error_reporting(E_ALL);


    define('ROOT', __DIR__);
    define('DS', DIRECTORY_SEPARATOR);
    

    function pr($data){
        echo "<pre>";
        print_r($data, true);
        echo "</pre>";
    }

    require ROOT . '/vendor/autoload.php';
    