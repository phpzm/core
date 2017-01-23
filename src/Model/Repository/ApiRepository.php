<?php

namespace Simples\Core\Model\Repository;

use Simples\Core\Data\Record;
use Simples\Core\Data\Collection;
use Simples\Core\Data\Validator;
use Simples\Core\Model\AbstractModel;
use Simples\Core\Model\Action;
use Simples\Core\Persistence\Transaction;

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
     * @param Record $record
     * @return Record
     */
    public function post(Record $record): Record
    {
        $defaults = $this->model->getDefaults(Action::CREATE);
        foreach ($defaults as $field => $default) {
            $record->set($field, $default);
        }

        $validators = $this->model->getValidators(Action::CREATE, $record);
        $errors = $this->getValidator()->parse($validators);
        if (!$errors->isEmpty()) {
            $this->setErrors($errors);
            return new Record([]);
        }

        $posting = $this->model->create($record);
        if ($posting) {
            return $posting;
        }
        return new Record([]);
    }

    /**
     * @param Record $record
     * @param int $start
     * @param int $end
     * @return Collection
     */
    public function get(Record $record, $start = null, $end = null): Collection
    {
        if (!is_null($start) && !is_null($end)) {
            $this->model->limit([$start, $end]);
        }
        $getting = $this->model->read($record);
        if ($getting) {
            return $getting;
        }
        return new Collection([]);
    }

    /**
     * @param Record|array $record
     * @return Record
     */
    public function put($record): Record
    {
        $defaults = $this->model->getDefaults(Action::UPDATE);
        foreach ($defaults as $field => $default) {
            $record->set($field, $default);
        }

        $validators = $this->model->getValidators(Action::UPDATE, $record);
        $errors = $this->getValidator()->parse($validators);
        if (!$errors->isEmpty()) {
            $this->setErrors($errors);
            return new Record([]);
        }

        $putting = $this->model->update($record);
        if ($putting) {
            return $putting;
        }
        return new Record([]);
    }

    /**
     * @param Record|array $record
     * @return Record
     */
    public function delete($record): Record
    {
        $deleting = $this->model->destroy($record);
        if ($deleting) {
            return $deleting;
        }
        return new Record([]);
    }

    /**
     * @param $action
     * @return array
     */
    public function getFields($action)
    {
        return $this->model->getFields($action);
    }

    /**
     * @param $logging
     */
    public function setLog($logging)
    {
        Transaction::log($logging && env('TEST_MODE'));
    }

    /**
     * @return string
     */
    public function getHashKey()
    {
        return $this->model->getHashKey();
    }
}
