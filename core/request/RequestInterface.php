<?php

namespace Core\Request;

interface RequestInterface
{
    function getRequestMethod();
    function setRequestMethod($method);
    function getContentType();
    function setContentType($contentType);
    function getUri();
    function setUri($uri);
    function getFiles();
    function setFiles($files);
    function getHeader(): array;
    function getHeaderParameter($parameter): string;
    function setHeader($header);
    function getBody(): array;
    function setBody($bodyContent);
}