<?php

namespace Simples\Core\Model;

class FieldContract
{
    /**
     * @var boolean
     */
    protected $primaryKey;

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
     * @var string
     */
    protected $label = '';
    protected $from = '';

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
    protected $referenced = [];
    protected $references = [];

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
     * @return array
     */
    public function getReferences(): array
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
     * @return string
     */
    public function getFrom(): string
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
        $this->from = '';
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
