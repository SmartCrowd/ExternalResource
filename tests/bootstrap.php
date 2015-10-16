<?php
ob_start();
define('ROOT_PATH',  str_replace('tests', '', __DIR__));

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
