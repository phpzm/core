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
     * @param Record|array $record
     * @return null|Record
     * @throws \Exception
     */
    final public function create($record = null)
    {
        if (!$record) {
            throw new \Exception('Create in DataMapper require parameters');
        }
        if (is_array($record)) {
            $record = new Record($record);
        }

        $action = Action::CREATE;

        if ($this->before($action, $record)) {
            if (!$record->get($this->hashKey)) {
                $record->set($this->hashKey, $this->hashKey());
            }

            $fields = [];
            $values = [];
            foreach ($record->all() as $field => $value) {
                if (!is_null($value)) {
                    $fields[] = $field;
                    $values[] = $value;
                }
            }
            foreach ($this->createKeys as $type => $timestampsKey) {
                $fields[] = $timestampsKey;
                $values[] = $this->getTimestampValue($type);
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
    final public function read($record = null)
    {
        if ($record) {
            if (is_array($record)) {
                $record = new Record($record);
            }
        }

        $action = Action::READ;

        if ($this->before($action, $record)) {
            $where = [];
            $filters = [];
            if (!$record->isEmpty()) {
                $where = $this->parseReadFilterFields($record->all());
                $filters = $this->parseReadFilterValues($record->all());
            }
            if ($this->destroyKeys) {
                $where[] = "(({$this->destroyKeys['at']} IS NULL) OR (NOT {$this->destroyKeys['at']}))";
            }

            $collection = $this
                ->collection($this->collection)
                ->fields($this->parseReadFields())
                ->where($where)
                ->get($filters);
            $after = new Record(['collection' => $collection]);
            if ($this->after($action, $after)) {
                return new Collection($after->get('collection'));
            }
        }
        return null;
    }

    /**
     * @param Record|array $record
     * @return null|Record
     * @throws \Exception
     */
    final public function update($record = null)
    {
        if (!$record) {
            throw new \Exception('Update in DataMapper require parameters');
        }
        if (is_array($record)) {
            $record = new Record($record);
        }

        $action = Action::UPDATE;

        $filter = [];
        if ($record->get($this->primaryKey)) {
            $filter = [$this->primaryKey => $record->get($this->primaryKey)];
        } elseif ($record->get($this->hashKey)) {
            $filter = [$this->hashKey => $record->get($this->hashKey)];
        }

        $previous = $this->read($filter)->current();
        if ($previous->isEmpty()) {
            return null;
        }

        $record->set($this->primaryKey, $previous->get($this->primaryKey));
        $record->set($this->hashKey, $previous->get($this->hashKey));

        if ($this->before($action, $record, $previous)) {
            $fields = [];
            $values = [];

            foreach ($record->all([$this->hashKey, $this->primaryKey]) as $field => $value) {
                if (!is_null($value)) {
                    $fields[] = $field;
                    $values[] = $value;
                }
            }
            foreach ($this->updateKeys as $type => $timestampsKey) {
                $fields[] = $timestampsKey;
                $values[] = $this->getTimestampValue($type);
            }

            $updated = $this
                ->collection($this->collection)
                ->fields($fields)
                ->where(["{$this->primaryKey} = ?"])
                ->set($values, [$record->get($this->primaryKey)]);

            if ($updated) {
                foreach ($record->all() as $name => $value) {
                    $previous->set($name, $value);
                }
                $record = $previous;
                if ($this->after($action, $record)) {
                    return $record;
                }
            }
        }
        return null;
    }

    /**
     * @param null $record
     * @return null|Record
     * @throws \Exception
     */
    final public function destroy($record = null)
    {
        if (!$record) {
            throw new \Exception('Destroy in DataMapper require parameters');
        }
        if (is_array($record)) {
            $record = new Record($record);
        }

        $action = Action::DESTROY;

        $filter = [];
        if ($record->get($this->primaryKey)) {
            $filter = [$this->primaryKey => $record->get($this->primaryKey)];
        } elseif ($record->get($this->hashKey)) {
            $filter = [$this->hashKey => $record->get($this->hashKey)];
        }

        $previous = $this->read($filter)->current();
        if ($previous->isEmpty()) {
            return null;
        }

        $record->set($this->primaryKey, $previous->get($this->primaryKey));
        $record->set($this->hashKey, $previous->get($this->hashKey));

        if ($this->before($action, $record, $previous)) {
            $where = ["{$this->primaryKey} = ?"];

            if ($this->destroyKeys) {
                $fields = [];
                $values = [];
                foreach ($this->destroyKeys as $type => $deletedKey) {
                    $fields[] = $deletedKey;
                    $values[] = $this->getTimestampValue($type);
                }

                $removed = $this
                    ->collection($this->collection)
                    ->fields($fields)
                    ->where($where)
                    ->set($values, [$record->get($this->primaryKey)]);
            } else {
                $removed = $this
                    ->collection($this->collection)
                    ->where($where)
                    ->remove([$record->get($this->primaryKey)]);
            }

            if ($removed) {
                foreach ($record->all() as $name => $value) {
                    $previous->set($name, $value);
                }
                $record = $previous;
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
    protected function parseReadFields()
    {
        if (off($this->getClausules(), 'fields')) {
            $fields = off($this->getClausules(), 'fields');
            $this->fields(null);
            return $fields;
        }
        return array_keys($this->getFields(Action::READ));
    }

    /**
     * @param $data
     * @return array
     */
    protected function parseReadFilterFields($data)
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
    protected function parseReadFilterValues($data)
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
                return Date::create()->now();
                break;
            case 'by':
                return Auth::getEmbedValue();
                break;
        }
        return null;
    }
}
