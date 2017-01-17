<?php

namespace Simples\Core\Persistence;

use \PDO;
use \PDOStatement;

/**
 * Class Connection
 * @package Simples\Core\Persistence
 */
abstract class SQLConnection extends Connection
{
    /**
     * @return PDO
     */
    protected function connect()
    {
        if (!$this->resource) {
            $default = [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_PERSISTENT => true,
            ];
            $options = array_merge($this->settings['options'], $default);
            $this->resource = new PDO(
                $this->dsn(), $this->settings['user'], $this->settings['password'], $options
            );
        }
        return $this->resource;
    }

    /**
     * @return string
     */
    abstract protected function dsn();

    /**
     * @param $sql
     * @return PDOStatement
     */
    final protected function statement($sql)
    {
        return $this->connect()->prepare($sql);
    }

    /**
     * @param $sql
     * @param array $values
     * @return int|null
     */
    final protected function execute($sql, array $values)
    {
        $statement = $this->statement($sql);

        if ($statement && $statement->execute(array_values($values))) {
            return $statement->rowCount();
        }

        return null;
    }
}
