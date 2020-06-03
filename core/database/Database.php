<?php

namespace Core\Database;

use Core\Database\DatabaseInterface;
use Core\Registry;
use Exception;
use PDO;
use PDOException;

class Database implements DatabaseInterface
{
    private static $instance = null;
    private $pdo;

    private function __construct()
    {
        $this->connect();
    }

    public static function getInstance()
    {
        if (!isset(self::$instance)) {
            self::$instance = new Database();
        }
        return self::$instance;
    }

    public function connect()
    {
        try {
            $this->pdo = new PDO($this->getDsn(), $this->getDbUser(), $this->getdbPass());
        } catch (PDOException $exception) {
            throw new Exception("Unable to connect to database!" . $exception->getMessage());
        }
    }

    public function disconnect(): void
    {
        $this->pdo = null;
    }

    public function executeQuery(string $sql, $params = NULL)
    {
        $statement = $this->pdo->prepare($sql);
        $statement->execute($params);
        return $statement;
    }

    private function getDsn(): string
    {
        $dsn = Registry::Config('mysqlDb');
        if (array_key_exists('dsn', $dsn) && (!empty($dsn['dsn']))) {
            return $dsn['dsn'];
        } else {
            throw new Exception("dsn array key in config file is not correct or is an empty string!");
        }
    }

    private function getDbUser(): string
    {
        $dbuser = Registry::Config('mysqlDb');
        if (array_key_exists('dbuser', $dbuser) && (!empty($dbuser['dbuser']))) {
            return $dbuser['dbuser'];
        } else {
            throw new Exception("dbuser array key in config file is not correct or is an empty string!");
        }
    }

    private function getDbPass(): string
    {
        $dbpass = Registry::Config('mysqlDb');
        if (array_key_exists('dbpass', $dbpass) && (!empty($dbpass['dbpass']))) {
            return $dbpass['dbpass'];
        } else {
            throw new Exception("dbpass array key in config file is not correct or is an empty string!");
        }
    }

    public function getPdo(): PDO
    {
        return $this->pdo;
    }

    public function __invoke(): PDO
    {
        return $this->getPdo();
    }
}
