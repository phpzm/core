<?php

namespace Simples\Core\Model\Repository;

use Simples\Core\Data\Collection;
use Simples\Core\Data\Record;
use Simples\Core\Data\Validator;
use Simples\Core\Data\Error\ValidationError;
use Simples\Core\Kernel\Container;
use Simples\Core\Model\AbstractModel;
use Simples\Core\Model\Action;

/**
 * Class ModelRepository
 * @package Simples\Core\Model\Repository
 */
class ModelRepository
{
    /**
     * @var AbstractModel
     */
    protected $model;

    /**
     * @var Validator
     */
    private $validator;

    /**
     * ApiRepository constructor.
     * @param AbstractModel $model
     * @param Validator|null $validator
     */
    public function __construct(AbstractModel $model, Validator $validator = null)
    {
        $this->model = $model;

        $this->validator = $validator ?? new Validator();
    }

    /**
     * @return $this
     */
    public static function box()
    {
        return Container::box()->make(static::class);
    }

    /**
     * @return AbstractModel
     */
    public function getModel(): AbstractModel
    {
        return $this->model;
    }

    /**
     * @return Validator
     */
    public function getValidator(): Validator
    {
        return $this->validator;
    }

    /**
     * @param $record
     * @return Record
     */
    public function unique($record = null): Record
    {
        $exists = $this->model->read($record);
        if ($exists->size()) {
            return $exists->current();
        }
        return $this->model->create($record);
    }

    /**
     * @param Record|array $record
     * @return Record
     * @throws ValidationError
     */
    public function create($record): Record
    {
        $record = Record::parse($record);

        $action = Action::CREATE;

        $this->model->configureFields($action, $record);

        $defaults = $this->model->getDefaults($action, $record);
        foreach ($defaults as $field => $default) {
            if (!$record->has($field)) {
                $record->set($field, $default);
            }
        }

        $validators = $this->model->getValidators($action, $record);
        $errors = $this->parseValidation($validators);
        if (!$errors->isEmpty()) {
            throw new ValidationError($errors->all());
        }

        return $this->model->create($record);
    }

    /**
     * @param Record|array $record
     * @param int $start
     * @param int $end
     * @return Collection
     */
    public function read($record, $start = null, $end = null): Collection
    {
        $record = Record::parse($record);

        if (!is_null($start) && !is_null($end)) {
            $this->model->limit([$start, $end]);
        }
        return $this->model->read($record);
    }

    /**
     * @param Record|array $record
     * @return Record
     * @throws ValidationError
     */
    public function update($record): Record
    {
        $record = Record::parse($record);

        $action = Action::UPDATE;

        $this->model->configureFields($action, $record);

        $defaults = $this->model->getDefaults($action, $record);
        foreach ($defaults as $field => $default) {
            if (!$record->has($field)) {
                $record->set($field, $default);
            }
        }

        $validators = $this->model->getValidators($action, $record);
        $errors = $this->parseValidation($validators);
        if (!$errors->isEmpty()) {
            throw new ValidationError($errors->all());
        }

        return $this->model->update($record);
    }

    /**
     * @param Record|array $record
     * @return Record
     * @throws ValidationError
     */
    public function destroy($record): Record
    {
        $record = Record::parse($record);

        $action = Action::DESTROY;

        $this->model->configureFields($action, $record);

        $validators = $this->model->getValidators($action, $record);
        $errors = $this->parseValidation($validators);
        if (!$errors->isEmpty()) {
            throw new ValidationError($errors->all());
        }

        return $this->model->destroy($record);
    }

    /**
     * @param array $filters
     * @param array $fields
     * @return Collection
     */
    public function find(array $filters, array $fields): Collection
    {
        return  $this->model->fields($fields)->read(Record::make($filters));
    }

    /**
     * @param Record $record
     * @param array $binds
     * @return Record
     */
    public function transform(Record $record, array $binds): Record
    {
        $transformed = [];
        foreach ($binds as $key => $value) {
            $transformed[$value] = $record->get($key);
        }
        return Record::make($transformed);
    }

    /**
     * @return array
     */
    public function getFields(): array
    {
        return $this->model->getFields(Action::RECOVER);
    }

    /**
     * @return string
     */
    public function getHashKey(): string
    {
        return $this->model->getHashKey();
    }

    /**
     * @param array $record
     * @return int
     */
    public function count(array $record) : int
    {
        return $this->model->count(Record::make($record));
    }

    /**
     * @param $validators
     * @return Record
     */
    private function parseValidation($validators)
    {
        return $this->getValidator()->parse($validators);
    }

    /**
     * @param bool $logging
     * @return ModelRepository
     */
    public function log($logging = true): ModelRepository
    {
        $this->model->log($logging);
        return $this;
    }
}
