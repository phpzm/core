<?php

namespace Simples\Core\Database;

use \PDO;
use Simples\Core\Flow\Wrapper;

/**
 * Class SQLDriver
 * @package Simples\Core\Database
 */
abstract class SQLDriver extends SQLConnection implements Driver
{
    /**
     * @var mixed
     */
    protected $hashKey = '';

    /**
     * @var mixed
     */
    protected $deletedKey = '';

    /**
     * @var array
     */
    protected $timestampsKeys = [];

    /**
     * SQLDriver constructor.
     * @param array $settings
     * @param string $hashKey
     * @param string $deletedKey
     * @param array $timestampsKeys
     */
    public function __construct(array $settings, $hashKey = '', $deletedKey = '', array $timestampsKeys = [])
    {
        parent::__construct($settings);

        $this->hashKey = $hashKey;
        $this->deletedKey = $deletedKey;
        $this->timestampsKeys = $timestampsKeys;
    }

    /**
     * @param $clausules
     * @param array $values
     * @return null|string
     */
    public final function create($clausules, array $values)
    {
        $sql = $this->getInsert($clausules);
        if (off($clausules, 'log')) {
            Wrapper::log($sql, $values);
        }
        $statement = $this->statement($sql);

        if ($statement && $statement->execute(array_values($values))) {
            return $this->connect()->lastInsertId();
        }

        return null;
    }

    /**
     * @param $clausules
     * @return string
     */
    public function getInsert($clausules)
    {
        $collection = off($clausules, 'collection', '<collection>');
        $fields = off($clausules, 'fields', '<fields>');

        $inserts = array_map(function () {
            return '?';
        }, $fields);

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
     * @param array $values
     * @return array|null
     */
    public final function read($clausules, array $values = [])
    {
        $sql = $this->getSelect($clausules);
        if (off($clausules, 'log')) {
            Wrapper::log($sql, $values);
        }
        $statement = $this->statement($sql);

        if ($statement && $statement->execute(array_values($values))) {
            return $statement->fetchAll(PDO::FETCH_ASSOC);
        }
        return null;
    }

    /**
     * @param $clausules
     * @return string
     */
    public function getSelect($clausules)
    {
        $collection = off($clausules, 'collection', '<collection>');
        $fields = off($clausules, 'fields', '*');
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
     * @param $values
     * @param $filters
     * @return int
     */
    public final function update($clausules, $values, $filters)
    {
        $sql = $this->getUpdate($clausules);
        if (off($clausules, 'log')) {
            Wrapper::log($sql, $values);
        }
        return $this->execute($sql, array_merge($values, $filters));
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

        $clausules = [
            'where' => [
                'instruction' => 'WHERE',
                'separator' => ' ',
            ]
        ];
        foreach ($clausules as $key => $clausule) {
            $value = off($clausule, $key);
            if (is_array($value)) {
                $value = implode($clausule['separator'], $value);
            }
            $command[] = $clausule['instruction'] . ' ' . $value;
        }

        return implode(' ', $command);
    }

    /**
     * @param $clausules
     * @param array $values
     * @return int|null
     */
    public final function destroy($clausules, array $values)
    {
        $sql = $this->getDelete($clausules);
        if (off($clausules, 'log')) {
            Wrapper::log($sql, $values);
        }
        return $this->execute($sql, $values);
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