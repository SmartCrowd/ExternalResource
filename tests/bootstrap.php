<?php
use Phalcon\DI,
    Phalcon\DI\FactoryDefault;

ini_set('display_errors', 1);
error_reporting(E_ALL);

define('ROOT_PATH',  str_replace('/tests', '', __DIR__));

/**
 * @param string $path
 * @param bool $isRequire
 * @return mixed
 */
function loadDepends($path, $isRequire = false){
    try {
        if ($isRequire)
            return require_once(ROOT_PATH . $path);
        else
            return include_once(ROOT_PATH . $path);
    } catch (\Exception $e) {
        return false;
    }
}

loadDepends("/vendor/autoload.php");

$config = loadDepends('/app/config/config.php');

include_once(ROOT_PATH . "/app/config/loader.php");
include_once(ROOT_PATH . "/app/config/services.php");