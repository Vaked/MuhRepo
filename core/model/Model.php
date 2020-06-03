<?php

namespace Core\Model;

use Core\Database\Database;
use Core\Registry;
use Exception;
use PDO;


class Model implements ModelInterface
{
    protected $tableName = null;
    protected $primaryKey = null;

    public function __construct($properties = [], $primaryKey = 'id')
    {
        $this->setTableName();
        $this->setPrimaryKey($primaryKey);
        $this->{$this->primaryKey} = 0;
        $this->mapProperties($properties);
    }

    private function setTableName()
    {
        if (!isset($this->tableName)) {
            $classPath = get_class($this);
            $classParts = explode('\\', $classPath);
            if (strlen($this->tableName) == 0) {
                $this->tableName = strtolower(end($classParts));
            }
        }
        return $this;
    }

    private function setPrimaryKey($prKey): void
    {
        if ($prKey) {
            $this->primaryKey = $prKey;
        } else {
            throw new Exception("Invalid primary key.");
        }
    }

    private function mapProperties(array $properties)
    {
        $tableColumns = $this->getTableColumns();
        foreach ($properties as $key => $value) {
            if ($key !== $this->primaryKey && (!array_key_exists($key, $tableColumns))) {
                $this->{$key} = $value;
            } else {
                throw new Exception("Property already exists in the table, please check the input parameter.");
            }
        }
        return $this;
    }

    public function save()
    {
        $objectProperties = get_object_vars($this);
        $tableColumns = $this->getTableColumns();
        $cleanArray = array_intersect_key($objectProperties, array_flip($tableColumns));

        if (!empty($cleanArray)) {

            $properties = array_keys($cleanArray);
            $values = array_values($cleanArray);

            $statement =
                "INSERT INTO {$this->tableName} (" . implode(
                    ", ",
                    $properties
                ) . ') VALUES (' . implode(', ', $values) . ')';

            Registry::Database()->executeQuery($statement);

            $lastInsertId = Registry::Database()->lastInsertId();
            $this->{$this->primaryKey} = $lastInsertId;
        } else {
            throw new Exception("Operation was not succesful, possible conflict with current properties");
        }
        return $this;
    }

    public function delete(): bool
    {
        $isExecuted = false;
        if ($this->{$this->primaryKey} !== 0) {
            $statement = "DELETE FROM {$this->tableName} WHERE `id` = {$this->{$this->primaryKey}}";
            $isExecuted = Registry::Database()->executeQuery($statement);
            if (!$isExecuted) {
                throw new Exception("Delete operation was not successful!");
            }
        }
        return $isExecuted;
    }

    public function update($data)
    {
        $objectProperties = get_object_vars($this);
        $tableColumns = $this->getTableColumns();
        $cleanArray = array_intersect_key($objectProperties, array_flip($tableColumns));

        $properties = array_keys($cleanArray);
        $values = array_values($cleanArray);

        $statement =
            "UPDATE {$this->tableName} SET (" . implode(
                ", ",
                $properties
            ) . ") (" . implode(", ", $values) . ") WHERE {$this->primaryKey} = {$this->{$this->primaryKey}}";
        $isExecuted = Registry::Dabatase()->executeQuery($statement);
        if (!$isExecuted) {
            throw new Exception("There has been an issue with the update querry, please check input data.");
        }
        return $this;
    }

    public function findBy($property, $value): array
    {
        if (property_exists($this, $property)) {
            $statement = "SELECT FROM {$this->tableName} WHERE {$property} = {$value} LIMIT 1";
            $result = Registry::Database()->executeQuery($statement);
            if (!$result) {
                throw new Exception("Find by operation was not successful!");
            }
            return $result->fetch(PDO::FETCH_ASSOC);
        }
        return [];
    }

    private function getTableColumns(): array
    {
        $statement = "DESCRIBE {$this->tableName}";
        $isExecuted = Registry::Database()->executeQuery($statement);
        $arrayOfColumns = [];
        while ($row = $isExecuted->fetch(PDO::FETCH_ASSOC)) {
            if ($row['Field'] !== $this->primaryKey) {
                $arrayOfColumns[] = $row['Field'];
            }
        }
        return $arrayOfColumns;
    }
}
