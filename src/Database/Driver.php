<?php

namespace Simples\Core\Database;

use \PDO;

/**
 * Class Driver
 * @package Simples\Core\Database
 */
abstract class Driver extends Connection
{
    /**
     * @param string $sql
     * @param array $values
     * @return string
     */
    public final function insert($sql, array $values)
    {
        $statement = $this->statement($sql);

        if ($statement && $statement->execute(array_values($values))) {
            return $this->connect()->lastInsertId();
        }

        return null;
    }

    /**
     * @param string $sql
     * @param array $values
     * @return array
     */
    public final function select($sql, array $values)
    {
        $statement = $this->statement($sql);

        if ($statement && $statement->execute(array_values($values))) {
            return $statement->fetchAll(PDO::FETCH_OBJ);
        }

        return null;
    }

    /**
     * @param string $sql
     * @param array $values
     * @return int
     */
    public final function update($sql, array $values)
    {
        return $this->execute($sql, $values);
    }

    /**
     * @param string $sql
     * @param array $values
     * @return int
     */
    public final function delete($sql, array $values)
    {
        return $this->execute($sql, $values);
    }

}