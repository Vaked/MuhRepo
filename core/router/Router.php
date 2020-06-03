<?php

namespace Core\Router;

use Core\Registry;
use Exception;

class Router implements RouterInterface
{
    private $routePath;
    private $route;
    private $uri;
    private $controller;
    private $action;
    private $method = [];
    private $params = [];

    public function __construct()
    {
        $this->init();
    }

    public function setUri(): void
    {
        $routeNames = array_keys($this->route);

        $uri = explode('/', urldecode(strtolower(ltrim($_SERVER['REQUEST_URI'], '/'))));

        if (!in_array($uri[0], $routeNames)) {
            throw new Exception("Invalid route!" . ' ' . $uri[0]);
        }

        $this->uri = $uri;
    }

    public function getUri(): array
    {
        return $this->uri;
    }

    public function setRoutePath(): void
    {
        try {
            (!key_exists($this->uri[0], $this->route));
        } catch (Exception $exception) {
            throw new Exception("Key is not part of the route configuration", 404);
        }
        $this->routePath = strtolower($this->uri[0]);
    }

    public function getRoutePath(): string
    {
        return $this->routePath;
    }

    public function setRequestMethod(): void
    {
        if (!key_exists($this->route[$this->routePath][1], $this->route[$this->routePath])) {
            $this->method = 'GET';
        }

        $this->method = $this->route[$this->routePath][1];
    }

    public function getRequestMethod()
    {
        return $this->method;
    }

    public function setController(): void
    {
        if (!key_exists($this->routePath, $this->route)) {
            throw new Exception("Unavailable resource, please check the url!", 404);
        }

        $configurationValues = explode('@', $this->route[$this->routePath][0]);

        if (empty($configurationValues[0])) {
            throw new Exception("Unavailable resource, action does not exist in the configuration!", 404);
        }

        $this->controller = new $configurationValues[0];
    }

    public function getController()
    {
        return $this->controller;
    }

    public function setAction(): void
    {

        if (!key_exists($this->routePath, $this->route)) {
            throw new Exception("Unavailable resource, please check the url!", 404);
        }

        $configurationValues = explode('@', strtolower($this->route[$this->routePath][0]));

        if (empty($configurationValues[1])) {
            throw new Exception("Unavailable resource, action does not exist in the configuration!", 404);
        }
        $this->action = $configurationValues[1];
    }

    public function getAction(): string
    {
        return $this->action;
    }

    public function setParams(): void
    {
        $paramCount = count($this->uri);
        for ($i = 2; $i < $paramCount; $i++) {
            $this->params[] = $this->uri[$i];
        }
    }

    public function getParams(): array
    {
        return $this->params;
    }

    public function callControllerAction()
    {
        if (!isset($this->controller) && !isset($this->action)) {
            throw new Exception("");
        }
        return  call_user_func_array([$this->controller, $this->action], $this->params);
    }

    public function init(): void
    {
        $this->route = Registry::Config('routes');
        $this->setUri();
        $this->setRoutePath();
        $this->setController();
        $this->setAction();
        $this->setRequestMethod();
    }
}
