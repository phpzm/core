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
     * Options used to configure the field
     * @var array
     */
    protected $options;

    /**
     * @var array
     */
    protected $validators;

    /**
     * @var string
     */
    protected $label;

    /**
     * @var boolean
     */
    protected $create;

    /**
     * @var boolean
     */
    protected $read;

    /**
     * @var boolean
     */
    protected $update;

    /**
     * @var array
     */
    protected $enum = [];

    /**
     * @var array
     */
    protected $referenced = [];

    /**
     * @var array
     */
    protected $references = [];

    /**
     * @var string
     */
    protected $from = '';

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
     * @return FieldContract
     */
    public function setCollection(string $collection): FieldContract
    {
        $this->collection = $collection;
        $this->from = '';
        return $this;
    }

    /**
     * @param string $name
     * @return FieldContract
     */
    public function setName(string $name): FieldContract
    {
        $this->name = $name;
        return $this;
    }

    /**
     * @param string $type
     * @return FieldContract
     */
    public function setType(string $type): FieldContract
    {
        $this->type = $type;
        return $this;
    }

    /**
     * @param array $options
     * @return FieldContract
     */
    public function setOptions(array $options): FieldContract
    {
        $this->options = $options;
        return $this;
    }

    /**
     * @param array $validators
     * @return FieldContract
     */
    public function setValidators(array $validators): FieldContract
    {
        $this->validators = $validators;
        return $this;
    }

    /**
     * @param string $label
     * @return FieldContract
     */
    public function setLabel(string $label): FieldContract
    {
        $this->label = $label;
        return $this;
    }

    /**
     * @param bool $create
     * @return FieldContract
     */
    public function create(bool $create): FieldContract
    {
        $this->create = $create;
        return $this;
    }

    /**
     * @param bool $read
     * @return FieldContract
     */
    public function read(bool $read): FieldContract
    {
        $this->read = $read;
        return $this;
    }

    /**
     * @param bool $update
     * @return FieldContract
     */
    public function update(bool $update): FieldContract
    {
        $this->update = $update;
        return $this;
    }
}
