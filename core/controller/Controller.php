<?php

namespace Core\Controller;

use Core\Registry;

class Controller
{
    private $request;

    public function __construct()
    {
        $this->request = Registry::IncomingRequest();
    }
}
