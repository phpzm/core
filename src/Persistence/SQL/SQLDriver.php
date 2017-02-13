<?php

namespace Simples\Core\Persistence\SQL;

use PDO;
use Simples\Core\Error\RunTimeError;
use Simples\Core\Model\Field;
use Simples\Core\Persistence\Driver;
use Simples\Core\Persistence\Filter;
use Simples\Core\Persistence\Fusion;
use Exception;
use ErrorException;

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
    public function start(): bool
    {
        return $this->connection()->beginTransaction();
    }

    /**
     * @return bool
     */
    public function commit(): bool
    {
        return $this->connection()->commit();
    }

    /**
     * @return bool
     */
    public function rollback(): bool
    {
        return $this->connection()->rollBack();
    }

    /**
     * @param array $clausules
     * @param array $values
     * @return string
     * @throws ErrorException
     */
    final public function create(array $clausules, array $values)
    {
        $sql = $this->getInsert($clausules);
        $this->addLog($sql, $values, off($clausules, 'log', false));
        $statement = $this->statement($sql);

        if ($statement && $statement->execute(array_values($values))) {
            return $this->connection()->lastInsertId();
        }
        throw new ErrorException(implode(', ', $statement->errorInfo()));
    }

    /**
     * @param array $clausules
     * @param array $values
     * @return array
     * @throws ErrorException
     */
    final public function read(array $clausules, array $values = [])
    {
        $sql = $this->getSelect($clausules);
        $this->addLog($sql, $values, off($clausules, 'log', false));
        $statement = $this->statement($sql);

        if ($statement && $statement->execute(array_values($values))) {
            return $statement->fetchAll(PDO::FETCH_ASSOC);
        }
        throw new ErrorException(implode(', ', $statement->errorInfo()));
    }

    /**
     * @param array $clausules
     * @param array $values
     * @param array $filters
     * @return int
     * @throws ErrorException
     */
    final public function update(array $clausules, array $values, array $filters)
    {
        $sql = $this->getUpdate($clausules);
        $parameters = array_merge(array_values($values), array_values($filters));

        $this->addLog($sql, $parameters, off($clausules, 'log', false));

        $statement = $this->statement($sql);

        if ($statement && $statement->execute($parameters)) {
            return $statement->rowCount();
        }
        throw new ErrorException(implode(', ', $statement->errorInfo()));
    }

    /**
     * @param array $clausules
     * @param array $values
     * @return int
     * @throws ErrorException
     */
    final public function destroy(array $clausules, array $values)
    {
        $sql = $this->getDelete($clausules);
        $this->addLog($sql, $values, off($clausules, 'log', false));

        $statement = $this->statement($sql);

        if ($statement && $statement->execute(array_values($values))) {
            return $statement->rowCount();
        }
        throw new ErrorException(implode(', ', $statement->errorInfo()));
    }

    /**
     * @param array $clausules
     * @return string
     */
    public function getInsert(array $clausules): string
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
     * @param array $clausules
     * @return string
     */
    public function getSelect(array $clausules): string
    {
        $table = off($clausules, 'source', '<source>');
        $columns = off($clausules, 'fields', '<fields>');
        $join = off($clausules, 'relation');

        $command = [];
        $command[] = 'SELECT';
        $command[] = $this->parseColumns($columns);
        $command[] = 'FROM';
        $command[] = $table;
        if ($join) {
            $command[] = $this->parseJoin($table, $join);
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
     * @param array $clausules
     * @return string
     */
    public function getUpdate(array $clausules): string
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
            $command[] = $this->parseJoin($table, $join);
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
     * @param array $clausules
     * @return string
     */
    public function getDelete(array $clausules): string
    {
        $table = off($clausules, 'source', '<source>');
        $join = off($clausules, 'relation');

        $command = [];
        $command[] = 'DELETE FROM';
        $command[] = $table;
        if ($join) {
            $command[] = $this->parseJoin($table, $join);
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
     * @param array $clausules
     * @param array $modifiers
     * @return array
     * @throws Exception
     */
    private function modifiers(array $clausules, array $modifiers): array
    {
        $command = [];
        foreach ($modifiers as $key => $modifier) {
            $value = off($clausules, $key);
            if ($value) {
                $key = ucfirst($key);
                $key = "parse{$key}";
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
    protected function parseFilter(array $filters, string $separator): string
    {
        $solver = new SQLSolverFilter();
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
    protected function parseGroup(array $groups, string $separator): string
    {
        return implode($separator, $groups);
    }

    /**
     * @param array $orders
     * @param string $separator
     * @return string
     */
    protected function parseOrder(array $orders, string $separator): string
    {
        return implode($separator, $orders);
    }

    /**
     * @param array $having
     * @param string $separator
     * @return string
     */
    protected function parseHaving(array $having, string $separator): string
    {
        return implode($separator, $having);
    }

    /**
     * @param $limits
     * @param $separator
     * @return string
     */
    protected function parseLimit($limits, $separator): string
    {
        return implode($separator, $limits);
    }

    /**
     * @param string $table
     * @param array $resources
     * @return string
     */
    protected function parseJoin(string $table, array $resources): string
    {
        $join = [];
        /** @var Fusion $resource */
        foreach ($resources as $resource) {
            $type = $resource->isExclusive() ? 'INNER' : 'LEFT';
            $collection = $resource->getCollection();
            $left = "`{$table}`.`{$resource->getReferenced()}`";
            $alias = '__' . strtoupper($resource->getReferenced()) . '__';
            $right = "`{$alias}`.`{$resource->getReferences()}`";
            $join[] = "{$type} JOIN `{$collection}` AS {$alias} ON ({$left} = {$right})";
        }

        return implode(' ', $join);
    }

    /**
     * @param $columns
     * @return string
     * @throws RunTimeError
     */
    protected function parseColumns($columns)
    {
        $type = gettype($columns);
        if ($type === TYPE_STRING) {
            return $columns;
        }
        if ($type === TYPE_ARRAY) {
            $solver = new SQLSolverColumn();
            $fields = [];
            foreach ($columns as $column) {
                $fields[] = $solver->render($column);
            }
            return implode(', ', $fields);
        }
        throw new RunTimeError("Columns must be an 'array' or 'string', {$type} given");
    }
}
