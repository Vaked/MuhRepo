<?php

function autoloader($class): void
{
    $explodedBySeparator = explode("\\", $class);
    $className = implode(DIRECTORY_SEPARATOR, $explodedBySeparator);
    $parentDirectory = dirname(__DIR__);
    $path = $parentDirectory . DIRECTORY_SEPARATOR . $className . ".php";
    if (file_exists($path)) {
        require_once($path);
    }
}

spl_autoload_register('autoloader');