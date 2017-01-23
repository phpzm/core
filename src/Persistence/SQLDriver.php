<?php

namespace Simples\Core\Persistence;

use \PDO;
use Simples\Core\Route\Wrapper;

/**
 * Class SQLDriver
 * @package Simples\Core\Persistence
 */
abstract class SQLDriver extends SQLConnection implements Driver
{
    /**
     * SQLDriver constructor.
     * @param array $settings
     */
    public function __construct(array $settings)
    {
        parent::__construct($settings);
    }

    /**
     * @return bool
     */
    public function start()
    {
        return $this->connect()->beginTransaction();
    }

    /**
     * @return bool
     */
    public function commit()
    {
        return $this->connect()->commit();
    }

    /**
     * @return bool
     */
    public function rollback()
    {
        return $this->connect()->rollBack();
    }

    /**
     * @param $clausules
     * @param array $values
     * @return string
     * @throws \ErrorException
     */
    final public function create($clausules, array $values)
    {
        $sql = $this->getInsert($clausules);
        $this->addLog($sql, $values, off($clausules, 'log'));
        $statement = $this->statement($sql);

        if ($statement && $statement->execute(array_values($values))) {
            return $this->connect()->lastInsertId();
        }
        throw new \ErrorException(implode(', ', $statement->errorInfo()));
    }

    /**
     * @param $clausules
     * @param array $values
     * @return array
     * @throws \ErrorException
     */
    final public function read($clausules, array $values = [])
    {
        $sql = $this->getSelect($clausules);
        $this->addLog($sql, $values, off($clausules, 'log'));
        $statement = $this->statement($sql);

        if ($statement && $statement->execute(array_values($values))) {
            return $statement->fetchAll(PDO::FETCH_ASSOC);
        }
        throw new \ErrorException(implode(', ', $statement->errorInfo()));
    }

    /**
     * @param $clausules
     * @param $values
     * @param $filters
     * @return int
     * @throws \ErrorException
     */
    final public function update($clausules, $values, $filters)
    {
        $sql = $this->getUpdate($clausules);
        $parameters = array_merge(array_values($values), array_values($filters));

        $this->addLog($sql, $parameters, off($clausules, 'log'));

        $statement = $this->statement($sql);

        if ($statement && $statement->execute($parameters)) {
            return $statement->rowCount();
        }
        throw new \ErrorException(implode(', ', $statement->errorInfo()));
    }

    /**
     * @param $clausules
     * @param array $values
     * @return int
     * @throws \ErrorException
     */
    final public function destroy($clausules, array $values)
    {
        $sql = $this->getDelete($clausules);
        $this->addLog($sql, $values, off($clausules, 'log'));

        $statement = $this->statement($sql);

        if ($statement && $statement->execute(array_values($values))) {
            return $statement->rowCount();
        }
        throw new \ErrorException(implode(', ', $statement->errorInfo()));
    }

    /**
     * @param $clausules
     * @return string
     */
    public function getInsert($clausules)
    {
        $collection = off($clausules, 'collection', '<collection>');
        $fields = off($clausules, 'fields', '<fields>');

        $inserts = [];
        foreach ($fields as $key => $field) {
            $inserts[] = '?';
        }

        $command = [];
        $command[] = 'INSERT INTO';
        $command[] = $collection;
        $command[] = '(' . (is_array($fields) ? implode(', ', $fields) : $fields) . ')';
        $command[] = 'VALUES';
        $command[] = '(' . implode(', ', $inserts) . ')';

        return implode(' ', $command);
    }

    /**
     * @param $clausules
     * @return string
     */
    public function getSelect($clausules)
    {
        $collection = off($clausules, 'collection', '<collection>');
        $fields = off($clausules, 'fields', '<fields>');
        $join = off($clausules, 'join');

        $command = [];
        $command[] = 'SELECT';
        $command[] = (is_array($fields) ? implode(', ', $fields) : $fields);
        $command[] = 'FROM';
        $command[] = $collection;
        if ($join) {
            $command[] = $join;
        }

        $modifiers = [
            'where' => [
                'instruction' => 'WHERE',
                'separator' => ' AND ',
            ],
            'group' => [
                'instruction' => 'GROUP BY',
                'separator' => ', ',
            ],
            'order' => [
                'instruction' => 'ORDER BY',
                'separator' => ', ',
            ],
            'having' => [
                'instruction' => 'HAVING',
                'separator' => ' AND ',
            ],
            'limit' => [
                'instruction' => 'LIMIT',
                'separator' => ',',
            ],
        ];
        $command = array_merge($command, $this->modifiers($clausules, $modifiers));

        return implode(' ', $command);
    }

    /**
     * @param $clausules
     * @return string
     */
    public function getUpdate($clausules)
    {
        $collection = off($clausules, 'collection', '<collection>');
        $join = off($clausules, 'join');
        $fields = off($clausules, 'fields', '<fields>');

        $sets = $fields;
        if (is_array($fields)) {
            $sets = implode(', ', array_map(function ($field) {
                return $field . ' = ?';
            }, $fields));
        }

        $command = [];
        $command[] = 'UPDATE';
        $command[] = $collection;
        if ($join) {
            $command[] = $join;
        }
        $command[] = 'SET';
        $command[] = $sets;

        $modifiers = [
            'where' => [
                'instruction' => 'WHERE',
                'separator' => ' AND ',
            ]
        ];
        $command = array_merge($command, $this->modifiers($clausules, $modifiers));

        return implode(' ', $command);
    }

    /**
     * @param $clausules
     * @return string
     */
    public function getDelete($clausules)
    {
        $table = off($clausules, 'collection', '<collection>');
        $join = off($clausules, 'join');

        $command = [];
        $command[] = 'DELETE FROM';
        $command[] = $table;
        if ($join) {
            $command[] = $join;
        }

        $modifiers = [
            'where' => [
                'instruction' => 'WHERE',
                'separator' => ' AND ',
            ]
        ];
        $command = array_merge($command, $this->modifiers($clausules, $modifiers));

        return implode(' ', $command);
    }

    /**
     * @param $clausules
     * @param $modifiers
     * @return array
     */
    private function modifiers($clausules, $modifiers)
    {
        $command = [];
        foreach ($modifiers as $key => $modifier) {
            $value = off($clausules, $key);
            if ($value) {
                if (is_array($value)) {
                    $value = implode($modifier['separator'], $value);
                }
                $command[] = $modifier['instruction'] . ' ' . $value;
            }
        }
        return $command;
    }
}
