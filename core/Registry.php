<?php

namespace Core;

use Exception;

class Registry
{
    private static $services = [];

    private function __construct()
    {
    }

    public static function getConfiguration()
    {
        $allowedServices = include(__DIR__ . '../../configurations/registryConfig.php');
        return $allowedServices['services'];
    }

    public static function set($key, $instance): void
    {
        $explodedKey = explode('\\', $key);
        $className = end($explodedKey);

        if (array_key_exists($key, self::getConfiguration()['singleton'])) {
            $allowedServices = self::getConfiguration()['singleton'];
        } elseif (array_key_exists($key, self::getConfiguration()['basic'])) {
            $allowedServices = self::getConfiguration()['basic'];
        } else {
            throw new Exception('Given key not valid, please check input parameters.');
        }

        $interfaces = class_implements($instance);

        if ($interfaces[$allowedServices[$key]] != $allowedServices[$key]) {
            throw new Exception("This class cannot be used by the registry as it is not part of the configuration.");
        }

        self::$services[$className] = $instance;
    }

    public static function __callStatic($name, $arguments)
    {
        if (array_key_exists($name, self::$services)) {
            if (count($arguments)) {
                return self::$services[$name](...$arguments);
            }
            return self::$services[$name];
        } else {
            throw new Exception('Object not found');
        }
    }

    public static function remove($key): void
    {
        unset(self::$services[$key]);
    }
}
