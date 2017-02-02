<?php

namespace Simples\Core\Model;

use Exception;
use Simples\Core\Data\Collection;
use Simples\Core\Data\Record;
use Simples\Core\Helper\Date;
use Simples\Core\Kernel\Container;
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
                ->table($this->getCollection())
                ->fields($fields)
                ->add($values);

            if ($created) {

                $primaryKey = $this->getPrimaryKey();
                if ($primaryKey) {
                    $record->set($primaryKey, $created);
                }

                $snapshot = clone $record;
                if ($this->after($action, $record)) {
                    if ($snapshot !== $record) {
                        $this->update($snapshot);
                    }
                    return $snapshot;
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
                $where[] = $this->getDestroyFilter();
            }

            $join = $this->parseReadRelations();

            $collection = $this
                ->table($this->getCollection())
                ->join($join)
                ->fields($this->parseReadFields())
                ->where($where)
                ->get($filters);

            $this->join(null);

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

        $previous = $this->previous($record);
        if ($previous->isEmpty()) {
            return null;
        }

        if ($this->before($action, $record, $previous)) {
            $fields = [];
            $values = [];

            foreach ($record->all([$this->hashKey, $this->getPrimaryKey()]) as $field => $value) {
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
                ->table($this->getCollection())
                ->fields($fields)
                ->where(["{$this->getPrimaryKey()} = ?"])
                ->set($values, [$record->get($this->getPrimaryKey())]);

            if ($updated) {
                foreach ($record->all() as $name => $value) {
                    $previous->set($name, $value);
                }
                $record = $previous;
                $snapshot = clone $record;
                if ($this->after($action, $record)) {
                    if ($snapshot !== $record) {
                        $this->update($snapshot);
                    }
                    return $snapshot;
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

        $previous = $this->previous($record);
        if ($previous->isEmpty()) {
            return null;
        }

        if ($this->before($action, $record, $previous)) {
            $where = ["{$this->getPrimaryKey()} = ?"];

            if ($this->destroyKeys) {
                $fields = [];
                $values = [];
                foreach ($this->destroyKeys as $type => $deletedKey) {
                    $fields[] = $deletedKey;
                    $values[] = $this->getTimestampValue($type);
                }

                $removed = $this
                    ->table($this->getCollection())
                    ->fields($fields)
                    ->where($where)
                    ->set($values, [$record->get($this->getPrimaryKey())]);
            } else {
                $removed = $this
                    ->table($this->getCollection())
                    ->where($where)
                    ->remove([$record->get($this->getPrimaryKey())]);
            }

            if ($removed) {
                foreach ($record->all() as $name => $value) {
                    $previous->set($name, $value);
                }
                $record = $previous;
                $snapshot = clone $record;
                if ($this->after($action, $record)) {
                    if ($snapshot !== $record) {
                        throw new Exception('Changes made after destroy are lost');
                    }
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
        $fields = array_keys($this->getFields(Action::READ));
        if (off($this->getClausules(), 'fields')) {
            $fields = off($this->getClausules(), 'fields');
            $this->fields(null);
        }
        $read = [];
        foreach ($fields as $field) {
            $value = "{$field}";
            if (strpos($field, '_') === 0) {
                $value = "{$this->getCollection()}.{$field}";
            }
            $read[] = $value;
        }
        return $read;
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

    /**
     * @return array
     */
    private function parseReadRelations(): array
    {
        $join = [];
        foreach ($this->fields as $field) {
            /** @var Field $field */
            $references = $field->getReferences();
            if (count($references)) {
                foreach ($references as $reference => $class) {
                    /** @var DataMapper $instance */
                    $instance = Container::getInstance()->make($class);
                    $table = $instance->getCollection();
                    $join[] = "LEFT JOIN {$table} ON ({$field->getName()} = {$reference})";
                }
            }
        }
        return $join;
    }

    /**
     * @param Record $record
     * @return Record
     */
    private function previous(Record $record): Record
    {
        $filter = [];
        if ($record->get($this->getPrimaryKey())) {
            $filter = [$this->getPrimaryKey() => $record->get($this->getPrimaryKey())];
        } elseif ($record->get($this->hashKey)) {
            $filter = [$this->hashKey => $record->get($this->hashKey)];
        }

        $previous = $this->read($filter)->current();
        if (!$previous->isEmpty()) {
            $record->set($this->getPrimaryKey(), $previous->get($this->getPrimaryKey()));
            $record->set($this->hashKey, $previous->get($this->hashKey));
        }

        return $previous;
    }

    /**
     * @return string
     */
    private function getDestroyFilter()
    {
        $field = "{$this->getCollection()}.{$this->destroyKeys['at']}";
        return "(({$field} IS NULL) OR (NOT {$field}))";
    }
}
