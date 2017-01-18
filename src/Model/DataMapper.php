<?php

namespace Simples\Core\Model;

use Simples\Core\Data\Collection;
use Simples\Core\Data\Record;
use Simples\Core\Helper\Date;
use Simples\Core\Security\Auth;

/**
 * Class DataMapper
 * @package Simples\Core\Model
 */
class DataMapper extends AbstractModel
{
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
            if (!$record->get($this->hashKey)) {
                $record->set($this->hashKey, uniqid());
            }

            $fields = [];
            $values = [];
            foreach ($record->all() as $field => $value) {
                if (!is_null($value)) {
                    $fields[] = $field;
                    $values[] = $value;
                }
            }
            foreach ($this->timestampsKeys as $event) {
                foreach ($event as $type => $timestampsKey) {
                    $fields[] = $timestampsKey;
                    $values[] = $this->getTimestampValue($type);
                }
            }

            $created = $this
                ->collection($this->collection)
                ->fields($fields)
                ->add($values);

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
        return false;
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

    /**
     * @param $type
     * @return null|string
     */
    protected function getTimestampValue($type)
    {
        switch ($type) {
            case 'at':
                return Date::create()->current();
                break;
            case 'by':
                return Auth::getEmbedValue();
                break;
        }
        return null;
    }
}
