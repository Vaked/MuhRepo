<?php

namespace Core\Router;

interface RouterInterface
{
    function getUri(): array;
    function getController();
    function setController(): void;
    function getAction(): string;
    function setAction(): void;
    function getParams(): array;
    function setParams(): void;
    function callControllerAction();
}
