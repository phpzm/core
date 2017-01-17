<?php

namespace Simples\Core\Model;

use Simples\Core\Data\Collection;
use Simples\Core\Data\Record;
use Simples\Core\Route\Wrapper;

/**
 * Class DataMapper
 * @package Simples\Core\Model
 */
class DataMapper extends AbstractModel
{
    /**
     * ActiveRecord constructor.
     */
    public function __construct()
    {
        parent::__construct($this->connection, $this->hashKey, $this->deletedKey, $this->timestampsKeys);
    }

    /**
     * @param mixed $record
     * @return Record|null
     */
    public final function create($record = null)
    {
        if (!$record) {
            return null;
        }
        if (is_array($record)) {
            $record = new Record($record);
        }

        $action = __FUNCTION__;

        if ($this->before($action, $record)) {

            $created = $this
                ->collection($this->collection)
                ->fields(array_keys($record->all()))
                ->add(array_values($record->all()));

            if ($created) {
                $primaryKey = is_array($this->primaryKey) ? $this->primaryKey[0] : $this->primaryKey;

                $record->set($primaryKey, $created);

                if ($this->after($action, $record)) {
                    return $record;
                }
            }
        }
        return null;
    }

    /**
     * @param mixed $record
     * @return Collection
     */
    public function read($record = null)
    {
        if ($record) {
            if (is_array($record)) {
                $record = new Record($record);
            }
        }
        $where = [];
        $values = [];
        if (!$record->isEmpty()) {
            $where = $this->parseFilterFields($record->all());
            $values = $this->parseFilterValues($record->all());
        }

        $array = $this
            ->collection($this->collection)
            ->fields($this->fieldsToRead())
            ->where($where)
            ->get($values);

        return new Collection($array);
    }

    /**
     * @param mixed $record
     * @return Record|null
     */
    public function update($record = null)
    {
        // set
        return new Record([]);
    }

    /**
     * @param mixed $record
     * @return Record|null
     */
    public function destroy($record = null)
    {
        if ($record) {
            if (is_array($record)) {
                $record = new Record($record);
            }
        }

        $action = __FUNCTION__;

        if ($this->before($action, $record)) {

            $where = [];
            $values = [];
            if (!$record->isEmpty()) {
                $where = ["{$this->primaryKey} = ?"];
                $values = [$record->get($this->primaryKey)];
            }

            $removed = $this
                ->collection($this->collection)
                ->where($where)
                ->remove($values);

            if ($removed) {
                if ($this->after($action, $record)) {
                    return $record;
                }
            }
        }

        return null;
    }

    /**
     * @param $record
     * @return mixed
     */
    public function fill($record)
    {
        if (!is_iterator($record)) {
            return false;
        }
        foreach ($record as $field => $value) {
            $this->$field = $value;
        }
        return $record;
    }

    /**
     * @return array
     */
    protected function fieldsToRead()
    {
        return '*';
    }

    /**
     * @param $data
     * @return array
     */
    protected function parseFilterFields($data)
    {
        $fields = [];
        foreach ($data as $key => $value) {
            $fields[] = "{$key} = ?";
        }
        return $fields;
    }

    /**
     * @param $data
     * @return array
     */
    protected function parseFilterValues($data)
    {
        return array_values($data);
    }

}
