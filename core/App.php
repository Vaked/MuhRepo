<?php

namespace Core;

use Core\Registry;
use Exception;

class App
{
  private static $instance = null;

  private function __construct()
  {
    $this->setRegistryConfigurations();
  }

  public function run()
  {
    try {
      Registry::Router()->callControllerAction();
    } catch (Exception $exception) {
      print($exception->getMessage());
      die;
    }
  }

  public function setRegistryConfigurations(): void
  {
    foreach (Registry::getConfiguration()['basic'] as $key => $value) {
      Registry::set($key, new $key());
    }

    foreach (Registry::getConfiguration()['singleton'] as $key => $value) {
      Registry::set($key, $key::getInstance());
    }
  }

  public static function getInstance()
  {
    if (self::$instance == null) {
      self::$instance = new App();
    }
    return self::$instance;
  }
}