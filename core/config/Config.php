<?php

namespace Core\Config;

use DirectoryIterator;
use Exception;

class Config implements ConfigInterface
{
    private static $instance;
    private $configurations = [];

    public function __construct()
    {
        $this->configurations = $this->getFilesFromConfigFolder();
    }

    public static function getInstance()
    {
        if (self::$instance === null) {
            self::$instance = new Config();
        }
        return self::$instance;
    }

    private function getFilesFromConfigFolder()
    {
        $files = new DirectoryIterator(__DIR__ . "/../../configurations/");
        foreach ($files as $file) {
            if ($file->isFile()) {
                try {
                    $this->configurations = array_merge($this->configurations, $this->readFile($file->getPathname()));
                } catch (Exception $exception) {
                    throw new Exception("Caught exception: " . $exception->getMessage() . PHP_EOL);
                }
            } else if (!$file->isDot()) {
                throw new Exception("Not a file!");
            }
        }
        return $this->configurations;
    }

    private function readFile($configFile)
    {
        if (!file_exists($configFile)) {
            throw new Exception("File does not exist, or cannot be read.");
        }

        $extension = pathinfo($configFile, PATHINFO_EXTENSION);
        if ($extension === "php") {
            $configurations = require($configFile);
        } else {
            throw new Exception("Cannot read config file format, please use .php");
        }
        return $configurations;
    }

    public function getConfiguration($configurationKey)
    {
        if (array_key_exists($configurationKey, $this->configurations)) {
            return $this->configurations[$configurationKey];
        } else {
            throw new Exception("Configuration key {$configurationKey} does not exist in configuration!");
        }
    }

    public function __invoke($configurationKey)
    {
        return $this->getConfiguration($configurationKey);
    }
}
