<?php

namespace Simples\Core\Model\Repository;

use Simples\Core\Data\Record;
use Simples\Core\Data\Collection;
use Simples\Core\Data\Validator;
use Simples\Core\Model\AbstractModel;

/**
 * Class ApiRepository
 * @package Simples\Core\Model\Repository
 */
class ApiRepository
{
    /**
     * @var AbstractModel
     */
    private $model;

    /**
     * @var Validator
     */
    private $validator;

    /**
     * @var Record
     */
    private $errors;

    /**
     * ApiRepository constructor.
     * @param AbstractModel $model
     * @param Validator|null $validator
     */
    public function __construct(AbstractModel $model, Validator $validator = null)
    {
        $this->model = $model;

        $this->validator = $validator ?? new Validator();
        $this->errors = new Record([]);
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
     * @param Record $errors
     * @return $this
     */
    public function setErrors(Record $errors)
    {
        $this->errors = $errors;
        return $this;
    }

    /**
     * @return Record
     */
    public function getErrors()
    {
        return $this->errors;
    }

    /**
     * @param $record
     * @param $log
     * @return Record
     */
    public function unique($record = null, $log = false): Record
    {
        $exists = $this->model->log($log)->read($record);
        if ($exists->size()) {
            return $exists->current();
        }
        return $this->model->create($record);
    }

    /**
     * @param Record|array $record
     * @param bool $log
     * @return Collection
     */
    public function get($record = null, $log = false): Collection
    {
        $getting = $this->model->log($log)->read($record);
        if ($getting) {
            return $getting;
        }
        return new Collection([]);
    }

    /**
     * @param Record|array $record
     * @param bool $log
     * @return Record
     */
    public function post($record, $log = false): Record
    {
        if (is_array($record)) {
            $record = new Record($record);
        }
        $defaults = $this->model->getDefaults('create');
        foreach ($defaults as $field => $default) {
            $record->set($field, $default);
        }

        $validators = $this->model->getValidators();
        $rules = [];
        foreach ($validators as $field => $validator) {
            $rules[$field] = ['rule' => $validator, 'value' => $record->get($field)];
        }

        $errors = $this->getValidator()->parse($rules);
        if (!$errors->isEmpty()) {
            $this->setErrors($errors);
            return new Record([]);
        }

        $posting = $this->model->log($log)->create($record);
        if ($posting) {
            return $posting;
        }
        return new Record([]);
    }

    /**
     * @param Record|array $record
     * @param bool $log
     * @return Record
     */
    public function put($record, $log = false): Record
    {
        $this->model->log = $log;

        return $record;
    }

    /**
     * @param Record|array $record
     * @param bool $log
     * @return Record
     */
    public function patch($record, $log = false): Record
    {
        $this->model->log = $log;

        return $record;
    }

    /**
     * @param Record|array $record
     * @param bool $log
     * @return Record
     */
    public function delete($record, $log = false): Record
    {
        $deleting = $this->model->log($log)->destroy($record);
        if ($deleting) {
            return $deleting;
        }
        return new Record([]);
    }

}