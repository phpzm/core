<?php

namespace Simples\Core\Database;

use \PDO;
use \PDOStatement;
use \Exception;

/**
 * Class Connection
 * @package Simples\Core\Database
 */
abstract class Connection
{
    /**
     * @var PDO
     */
    private $pdo = null;
    /**
     * @var array
     */
    protected $options = [];

    /**
     * Connection constructor.
     * @param array $options
     * @throws Exception
     */
    public function __construct(array $options)
    {
        $this->options = $options;
    }

    /**
     * @return PDO
     */
    protected function connect()
    {
        if (!$this->pdo) {
            $this->pdo = new PDO($this->dsn(), $this->options['user'], $this->options['password']);
        }
        return $this->pdo;
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

    /**
     * @return array
     */
    public function getOptions()
    {
        return $this->options;
    }

}