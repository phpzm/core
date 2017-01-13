<?php

namespace Simples\Core\Database;

use \PDO;
use \PDOStatement;

/**
 * Class Connection
 * @package Simples\Core\Database
 */
abstract class SQLConnection extends Connection
{
    /**
     * @return PDO
     */
    protected function connect()
    {
        if (!$this->resource) {
            $this->resource = new PDO($this->dsn(), $this->settings['user'], $this->settings['password']);
        }
        return $this->resource;
    }

    /**
     * @return string
     */
    protected abstract function dsn();

    /**
     * @param $sql
     * @return PDOStatement
     */
    protected final function statement($sql)
    {
        return $this->connect()->prepare($sql);
    }

    /**
     * @param $sql
     * @param array $values
     * @return int|null
     */
    protected final function execute($sql, array $values)
    {
        $statement = $this->statement($sql);

        if ($statement && $statement->execute(array_values($values))) {
            return $statement->rowCount();
        }

        return null;
    }

}