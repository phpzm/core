<?php

namespace Simples\Core\Persistence\SQL;

use PDO;
use Simples\Core\Persistence\Driver;
use Simples\Core\Persistence\Filter;
use Simples\Core\Persistence\Fusion;
use Exception;

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
        $source = off($clausules, 'source', '<source>');
        $fields = off($clausules, 'fields', '<fields>');

        $inserts = [];
        foreach ($fields as $key => $field) {
            $inserts[] = '?';
        }

        $command = [];
        $command[] = 'INSERT INTO';
        $command[] = $source;
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
        $table = off($clausules, 'source', '<source>');
        $columns = off($clausules, 'fields', '<fields>');
        $join = off($clausules, 'relation');

        $command = [];
        $command[] = 'SELECT';
        $command[] = (is_array($columns) ? implode(', ', $columns) : $columns);
        $command[] = 'FROM';
        $command[] = $table;
        if ($join) {
            $command[] = $this->parseJoin($join);
        }

        $modifiers = [
            'filter' => [
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
        $table = off($clausules, 'source', '<source>');
        $join = off($clausules, 'relation');
        $columns = off($clausules, 'fields', '<fields>');

        $sets = $columns;
        if (is_array($columns)) {
            $sets = implode(', ', array_map(function ($field) {
                return $field . ' = ?';
            }, $columns));
        }

        $command = [];
        $command[] = 'UPDATE';
        $command[] = $table;
        if ($join) {
            $command[] = $this->parseJoin($join);
        }
        $command[] = 'SET';
        $command[] = $sets;

        $modifiers = [
            'filter' => [
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
        $source = off($clausules, 'source', '<source>');
        $join = off($clausules, 'relation');

        $command = [];
        $command[] = 'DELETE FROM';
        $command[] = $source;
        if ($join) {
            $command[] = $this->parseJoin($join);
        }

        $modifiers = [
            'filter' => [
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
     * @throws Exception
     */
    private function modifiers($clausules, $modifiers)
    {
        $command = [];
        foreach ($modifiers as $key => $modifier) {
            $value = off($clausules, $key);
            if ($value) {
                if (!method_exists($this, $key)) {
                    throw new Exception("Invalid modifier {$key}");
                }
                $value = $this->$key($value, $modifier['separator']);
                $command[] = $modifier['instruction'] . ' ' . $value;
            }
        }
        return $command;
    }

    /**
     * @param array $filters
     * @param string $separator
     * @return string
     */
    protected function filter(array $filters, string $separator): string
    {
        $solver = new SQLFilterSolver();
        $parsed = [];
        foreach ($filters as $filter) {
            /** @var Filter $filter */
            $parsed[] = $solver->render($filter);
        }
        return implode($separator, $parsed);
    }

    /**
     * @param array $groups
     * @param string $separator
     * @return string
     */
    protected function group(array $groups, string $separator): string
    {
        return implode($separator, $groups);
    }

    /**
     * @param array $orders
     * @param string $separator
     * @return string
     */
    protected function order(array $orders, string $separator): string
    {
        return implode($separator, $orders);
    }

    /**
     * @param array $having
     * @param string $separator
     * @return string
     */
    protected function having(array $having, string $separator): string
    {
        return implode($separator, $having);
    }

    /**
     * @param $limits
     * @param $separator
     * @return string
     */
    protected function limit($limits, $separator): string
    {
        return implode($separator, $limits);
    }

    /**
     * @param array $resources
     * @return string
     */
    private function parseJoin(array $resources): string
    {
        $join = [];
        /** @var Fusion $resource */
        foreach ($resources as $resource) {
            $type = $resource->isExclusive() ? 'INNER' : 'LEFT';
            $table = $resource->getCollection();
            $left = $resource->getReferenced();
            $right = $resource->getReferences();
            $join[] = "{$type} JOIN {$table} ON ({$left} = {$right})";
        }

        return implode(' ', $join);
    }
}
