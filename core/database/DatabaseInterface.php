<?php

namespace Core\Database;

interface DatabaseInterface
{
    function connect();
    function disconnect(): void;
    function executeQuery(string $sql, $params);
}
