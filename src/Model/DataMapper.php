<?php

namespace Simples\Core\Model;

use Exception;
use Simples\Core\Data\Collection;
use Simples\Core\Data\Record;
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
                ->source($this->getCollection())
                ->fields($fields)
                ->add($values);

            if ($created) {

                $record->set($this->getPrimaryKey(), $created);
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
        if (!$record) {
            $record = [];
        }
        if (is_array($record)) {
            $record = new Record($record);
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
                ->fields($this->parseReadFields())
                ->filter($where)
                ->get($filters);

            $this->relation(null);

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

            $filter = new Filter($this->getField($this->getPrimaryKey()), $record->get($this->getPrimaryKey()));

            $updated = $this
                ->source($this->getCollection())
                ->fields($fields)
                ->filter([$filter])
                ->set($values, [$filter->getValue()]);

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

        $previous = $this->previous($record);
        if ($previous->isEmpty()) {
            return null;
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
     * @param Record|null $record
     * @return int
     */
    public function count(Record $record = null): int
    {
        $alias = 'count';
        $data = $this
            ->fields([
                new Field($this->getCollection(), $this->getPrimaryKey(), Field::AGGREGATOR_COUNT, ['alias' => $alias])
            ])
            ->limit(null)
            ->read($record);

        if (!$data->current()->isEmpty()) {
            return (int)$data->current()->get($alias);
        }
        return 0;
    }

    /**
     * @return array
     */
    protected function parseReadFields()
    {
        if (off($this->getClausules(), 'fields')) {
            $fields = off($this->getClausules(), 'fields');
            if (!is_array($fields)) {
                $fields = [$fields];
            }
            $this->fields(null);
        }
        if (!isset($fields)) {
            $fields = $this->getFields(Action::READ);
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
                throw new Exception("Invalid field name '{$name}'");
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
                    $join[] = new Fusion($field->getName(), $table, $reference);
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
    private function getDestroyFilter(): Filter
    {
        $field = new Field($this->getCollection(), $this->destroyKeys['at'], Field::TYPE_DATETIME);
        return new Filter($field, Filter::rule(null, Filter::RULE_BLANK));
    }
}
