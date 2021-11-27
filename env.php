<?php

require_once './vendor/autoload.php';

if($_SERVER['HTTP_HOST'] == "localhost") {
    define('DB_HOST','localhost');
    define('DB_USERNAME','root');
    define('DB_PASSWORD', '');
    define('DB_NAME', 'medicineapp');
} else {
    define('DB_HOST', 'localhost');
    define('DB_USERNAME', 'id17452090_aio_user');
    define('DB_PASSWORD', 'MoGZdLnI-hO2K1=)');
    define('DB_NAME', 'id17452090_aio_db');
}

/**
 * 
 * Define the environment of application
 * @value Local, Live
 * 
 */
define('APP_ENVIRONMENT', 'Local');

function pre($array, $exit = true) {
    echo "<pre>";
    print_r($array);
    
    if($exit) {
        exit;
    }
}