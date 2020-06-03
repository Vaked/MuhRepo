<?php

namespace Core\Response;

interface ResponseInterface

{
    function setStatus($status);
    function setBody($body);
    function setHeader($name, $value);
    function setContentType($contentType);
}