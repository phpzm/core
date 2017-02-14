<?php

namespace Simples\Core\Model;

use Exception;
use Simples\Core\Data\Collection;
use Simples\Core\Data\Record;
use Simples\Core\Error\RunTimeError;
use Simples\Core\Helper\Date;
use Simples\Core\Kernel\Container;
use Simples\Core\Persistence\Filter;
use Simples\Core\Persistence\Fusion;
use Simples\Core\Security\Auth;

/**
 * Class DataMapper
 * @package Simples\Core\Model
 */
class DataMapper extends AbstractModel
{
    /**
     * Method with the responsibility of create a record of model
     * @param array|Record $record (null)
     * @return Record
     * @throws RunTimeError
     */
    final public function create($record = null): Record
    {
        if (!$record) {
            throw new RunTimeError('Create in DataMapper require parameters');
        }
        if (is_array($record)) {
            $record = Record::make($record);
        }

        $action = Action::CREATE;

        if ($this->before($action, $record)) {
            if (!$record->get($this->hashKey)) {
                $record->set($this->hashKey, $this->hashKey());
            }

            $fields = [];
            $values = [];
            foreach ($this->getActionFields($action) as $field) {
                /** @var Field $field */
                $name = $field->getName();
                if ($field->isCalculated()) {
                    $value = $field->calculate($record);
                    $record->set($name, $value);
                }
                if ($record->has($name)) {
                    $value = $record->get($name);
                }
                if (isset($value)) {
                    $fields[] = $name;
                    $values[] = $value;
                    unset($value);
                }
            }
            foreach ($this->createKeys as $type => $timestampsKey) {
                $fields[] = $timestampsKey;
                $values[] = $this->getTimestampValue($type);
            }

            $created = $this
                ->source($this->getCollection())
                ->fields($fields)
                ->add($values);

            $this->reset();

            if ($created) {
                $record->set($this->getPrimaryKey(), $created);
                if ($this->after($action, $record)) {
                    return $record;
                }
            }
        }
        return Record::make([]);
    }

    /**
     * Read records with the filters informed
     * @param array|Record $record (null)
     * @return Collection
     */
    final public function read($record = null): Collection
    {
        if (!$record) {
            $record = [];
        }
        if (is_array($record)) {
            $record = Record::make($record);
        }

        $action = Action::READ;

        if ($this->before($action, $record)) {
            $where = [];
            $filters = [];
            if (!$record->isEmpty()) {
                $where = $this->parseReadFilterFields($record->all());
                $filters = $this->parseReadFilterValues($where);
            }

            if ($this->destroyKeys) {
                $where[] = $this->getDestroyFilter();
            }

            $relations = $this->parseReadRelations();

            $collection = $this
                ->source($this->getCollection())
                ->relation($relations)
                ->fields($this->getActionFields($action))
                ->filter($where)
                ->get($filters);

            $this->reset();

            $after = Record::make(['collection' => $collection]);
            if ($this->after($action, $after)) {
                return Collection::make($after->get('collection'));
            }
        }
        return Collection::make([]);
    }

    /**
     * Update the record given
     * @param array|Record $record (null)
     * @return Record
     * @throws RunTimeError
     */
    final public function update($record = null): Record
    {
        if (!$record) {
            throw new RunTimeError('Update in DataMapper require parameters');
        }
        if (is_array($record)) {
            $record = Record::make($record);
        }

        $action = Action::UPDATE;

        $previous = $this->previous($record);
        if ($previous->isEmpty()) {
            return $previous;
        }

        if ($this->before($action, $record, $previous)) {
            $fields = [];
            $values = [];
            foreach ($this->getActionFields($action) as $key => $field) {
                /** @var Field $field */
                $name = $field->getName();
                if ($field->isCalculated()) {
                    $value = $field->calculate($record);
                    $record->set($name, $value);
                }
                if ($record->has($name)) {
                    $value = $record->get($name);
                }
                if (isset($value)) {
                    $fields[] = $name;
                    $values[] = $value;
                }
            }
            foreach ($this->updateKeys as $type => $timestampsKey) {
                $fields[] = $timestampsKey;
                $values[] = $this->getTimestampValue($type);
            }

            $filter = new Filter($this->getField($this->getPrimaryKey()), $record->get($this->getPrimaryKey()));

            $updated = $this
                ->source($this->getCollection())
                ->fields($fields)
                ->filter([$filter])
                ->set($values, [$filter->getValue()]);

            $this->reset();

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
        return Record::make([]);
    }

    /**
     * Remove the given record of database
     * @param array|Record $record (null)
     * @return Record
     * @throws RunTimeError
     */
    final public function destroy($record = null): Record
    {
        if (!$record) {
            throw new RunTimeError('Destroy in DataMapper require parameters');
        }
        if (is_array($record)) {
            $record = Record::make($record);
        }

        $action = Action::DESTROY;

        $previous = $this->previous($record);
        if ($previous->isEmpty()) {
            return $previous;
        }

        if ($this->before($action, $record, $previous)) {
            $filter = new Filter($this->getField($this->getPrimaryKey()), $record->get($this->getPrimaryKey()));
            $filters = [$filter];

            if ($this->destroyKeys) {
                $fields = [];
                $values = [];
                foreach ($this->destroyKeys as $type => $deletedKey) {
                    $fields[] = $deletedKey;
                    $values[] = $this->getTimestampValue($type);
                }

                $removed = $this
                    ->source($this->getCollection())
                    ->fields($fields)
                    ->filter($filters)
                    ->set($values, [$filter->getValue()]);
            } else {
                $removed = $this
                    ->source($this->getCollection())
                    ->filter($filters)
                    ->remove([$record->get($this->getPrimaryKey())]);
            }

            $this->reset();

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
        return Record::make([]);
    }

    /**
     * Get total of records based on filters
     * @param array|Record $record (null)
     * @return int
     */
    public function count($record = null): int
    {
        // Record
        $alias = 'count';
        $data = $this
            ->fields([
                new Field($this->getCollection(), $this->getPrimaryKey(), Field::AGGREGATOR_COUNT, ['alias' => $alias])
            ])
            ->limit(null)
            ->read($record);

        $this->reset();

        if (!$data->current()->isEmpty()) {
            return (int)$data->current()->get($alias);
        }
        return 0;
    }

    /**
     * @param string $action
     * @return array|mixed
     */
    protected function getActionFields(string $action)
    {
        if (off($this->getClausules(), 'fields')) {
            $fields = off($this->getClausules(), 'fields');
            if (!is_array($fields)) {
                $fields = [$fields];
            }
            $this->fields(null);
        }
        if (!isset($fields)) {
            $fields = $this->getFields($action);
        }
        return $fields;
    }

    /**
     * @param array $data
     * @return array
     * @throws Exception
     */
    protected function parseReadFilterFields(array $data): array
    {
        $filters = [];
        foreach ($data as $name => $value) {
            $field = $this->getField($name);
            if (is_null($field)) {
                throw new RunTimeError("Invalid field name '{$name}'");
            }
            $filters[] = new Filter($field, $value);
        }
        return $filters;
    }

    /**
     * @param array $filters
     * @return array
     */
    protected function parseReadFilterValues(array $filters): array
    {
        $values = [];
        /** @var Filter $filter */
        foreach ($filters as $filter) {
            $value = $filter->getParsedValue();
            if (!is_array($value)) {
                $values[] = $value;
                continue;
            }
            $values = array_merge($values, $value);
        }
        return $values;
    }

    /**
     * @param string $type
     * @return null|string
     */
    protected function getTimestampValue(string $type)
    {
        switch ($type) {
            case 'at':
                return Date::now();
                break;
            case 'by':
                return Auth::getUser();
                break;
        }
        return null;
    }

    /**
     * @return array
     */
    protected function parseReadRelations(): array
    {
        $join = [];
        foreach ($this->fields as $field) {
            /** @var Field $field */
            $reference = $field->getReference();
            if (count($reference) === 2) {
                /** @var DataMapper $instance */
                $instance = Container::box()->make($reference['class']);
                $table = $instance->getCollection();
                $join[] = new Fusion($field->getName(), $table, $reference['target']);
            }
        }
        return $join;
    }

    /**
     * @param Record $record
     * @return Record
     */
    protected function previous(Record $record): Record
    {
        $hashKey = $this->hashKey;
        $primaryKey = $this->getPrimaryKey();

        $filter = [$hashKey => $record->get($hashKey)];
        if ($record->get($primaryKey)) {
            $filter = [$primaryKey => $record->get($primaryKey)];
        }

        $previous = $this->fields(null)->read($filter)->current();
        if (!$previous->isEmpty()) {
            $record->set($primaryKey, $previous->get($primaryKey));
            $record->set($hashKey, $previous->get($hashKey));
        }

        return $previous;
    }

    /**
     * @return Filter
     */
    protected function getDestroyFilter(): Filter
    {
        $field = new Field($this->getCollection(), $this->destroyKeys['at'], Field::TYPE_DATETIME);
        return new Filter($field, Filter::rule(null, Filter::RULE_BLANK));
    }
}
