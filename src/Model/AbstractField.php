<?php

namespace Simples\Core\Model;

use stdClass;

/**
 * Class AbstractField
 * @package Simples\Core\Model
 */
abstract class AbstractField
{
    /**
     * Collection to which this field belongs
     * @var string
     */
    protected $collection;

    /**
     * The name of field, used to create schemas and instructions
     * @var string
     */
    protected $name;

    /**
     * The type is useful to create schemas e apply validation rules e sanitizes
     * @var string
     */
    protected $type;

    /**
     * @var boolean
     */
    protected $primaryKey;

    /**
     * @var Field
     */
    protected $from;

    /**
     * @var string
     */
    protected $label = '';

    /**
     * @var mixed
     */
    protected $default;

    /**
     * @var boolean
     */
    protected $create;
    protected $read;
    protected $update;
    protected $recover;

    /**
     * Options used to configure the field
     * @var array
     */
    protected $options = [];
    protected $validators = [];
    protected $enum = [];

    /**
     * @var array
     */
    protected $referenced = [];

    /**
     * @var stdClass
     */
    protected $references;

    /**
     * @var callable
     */
    protected $calculated;

    /**
     * @return string
     */
    public function getCollection(): string
    {
        return $this->collection;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return string
     */
    public function getType(): string
    {
        return $this->type;
    }

    /**
     * @return bool
     */
    public function isPrimaryKey(): bool
    {
        return $this->primaryKey;
    }

    /**
     * @return array
     */
    public function getOptions(): array
    {
        return $this->options;
    }

    /**
     * @return array
     */
    public function getValidators(): array
    {
        return $this->validators;
    }

    /**
     * @return string
     */
    public function getLabel(): string
    {
        return $this->label;
    }

    /**
     * @return mixed
     */
    public function getDefault()
    {
        if (is_callable($this->default)) {
            $callable = $this->default;
            return $callable();
        }
        return $this->default;
    }

    /**
     * @return bool
     */
    public function isCreate(): bool
    {
        return $this->create;
    }

    /**
     * @return bool
     */
    public function isRead(): bool
    {
        return $this->read;
    }

    /**
     * @return bool
     */
    public function isUpdate(): bool
    {
        return $this->update;
    }

    /**
     * @return bool
     */
    public function isRecover(): bool
    {
        return $this->recover;
    }

    /**
     * @return bool
     */
    public function isCalculated(): bool
    {
        return is_callable($this->calculated);
    }

    /**
     * @return array
     */
    public function getReferenced(): array
    {
        return $this->referenced;
    }

    /**
     * @return stdClass
     */
    public function getReferences(): stdClass
    {
        return $this->references;
    }

    /**
     * @return array
     */
    public function getEnum(): array
    {
        return $this->enum;
    }

    /**
     * @return bool
     */
    public function hasFrom(): bool
    {
        return !!$this->from;
    }

    /**
     * @return Field
     */
    public function getFrom(): Field
    {
        return $this->from;
    }

    /**
     * @param string $collection
     * @return Field
     */
    public function setCollection(string $collection)
    {
        $this->collection = $collection;
        /** @noinspection PhpIncompatibleReturnTypeInspection */
        return $this;
    }

    /**
     * @param string $name
     * @return Field
     */
    public function setName(string $name)
    {
        $this->name = $name;
        /** @noinspection PhpIncompatibleReturnTypeInspection */
        return $this;
    }

    /**
     * @param string $type
     * @return Field
     */
    public function setType(string $type)
    {
        $this->type = $type;
        /** @noinspection PhpIncompatibleReturnTypeInspection */
        return $this;
    }

    /**
     * @param array $options
     * @return Field
     */
    public function setOptions(array $options)
    {
        $this->options = $options;
        /** @noinspection PhpIncompatibleReturnTypeInspection */
        return $this;
    }

    /**
     * @param array $validators
     * @return Field
     */
    public function setValidators(array $validators)
    {
        $this->validators = $validators;
        /** @noinspection PhpIncompatibleReturnTypeInspection */
        return $this;
    }

    /**
     * @param string $label
     * @return Field
     */
    public function setLabel(string $label)
    {
        $this->label = $label;
        /** @noinspection PhpIncompatibleReturnTypeInspection */
        return $this;
    }

    /**
     * @param bool $create
     * @return Field
     */
    public function create(bool $create)
    {
        $this->create = $create;
        /** @noinspection PhpIncompatibleReturnTypeInspection */
        return $this;
    }

    /**
     * @param bool $read
     * @return Field
     */
    public function read(bool $read)
    {
        $this->read = $read;
        /** @noinspection PhpIncompatibleReturnTypeInspection */
        return $this;
    }

    /**
     * @param bool $update
     * @return Field
     */
    public function update(bool $update)
    {
        $this->update = $update;
        /** @noinspection PhpIncompatibleReturnTypeInspection */
        return $this;
    }

    /**
     * @return Field
     */
    public function readonly()
    {
        return $this->create(false)->read(false)->update(false);
    }

    /**
     * @param bool $recover
     * @return Field
     */
    public function recover(bool $recover)
    {
        $this->recover = $recover;
        /** @noinspection PhpIncompatibleReturnTypeInspection */
        return $this;
    }
}
