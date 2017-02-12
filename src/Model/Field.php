<?php

namespace Simples\Core\Model;

/**
 * Class Field
 * @package Simples\Core\Model
 */
class Field
{
    /**
     * @var string
     */
    const TYPE_STRING = 'string', TYPE_DATETIME = 'datetime', TYPE_BOOLEAN = 'boolean',
        TYPE_DATE = 'date', TYPE_INTEGER = 'integer', TYPE_FLOAT = 'float', TYPE_TEXT = 'text', TYPE_FILE = 'file',
        TYPE_ARRAY = 'array';

    /**
     * @var string
     */
    const AGGREGATOR_COUNT = 'count';

    /**
     * @var boolean
     */
    private $primaryKey;

    /**
     * Collection to which this field belongs
     * @var string
     */
    private $collection;

    /**
     * The name of field, used to create schemas and instructions
     * @var string
     */
    private $name;

    /**
     * The type is useful to create schemas e apply validation rules e sanitizes
     * @var string
     */
    private $type;

    /**
     * Options used to configure the field
     * @var array
     */
    private $options;

    /**
     * @var array
     */
    private $validators;

    /**
     * @var string
     */
    private $label;

    /**
     * @var boolean
     */
    private $create;

    /**
     * @var boolean
     */
    private $read;

    /**
     * @var boolean
     */
    private $update;

    /**
     * @var array
     */
    private $enum = [];

    /**
     * @var array
     */
    private $referenced = [];

    /**
     * @var array
     */
    private $references = [];

    /**
     * @var callable
     */
    private $calculated;

    /**
     * Field constructor.
     * @param string $collection
     * @param string $name
     * @param string $type
     * @param array $options
     */
    public function __construct(string $collection, string $name, string $type, array $options = [])
    {
        $this->collection = $collection;
        $this->name = $name;
        $this->type = $type;
        $this->options = $options;

        $default = [
            'label' => '', 'validators' => [], 'create' => true, 'read' => true, 'update' => true,
            'enum' => [], 'referenced' => [], 'references' => [],
        ];
        $options = array_merge($default, $options);

        foreach ($options as $key => $value) {
            /** @noinspection PhpVariableVariableInspection */
            $this->$key = $value;
        }
        if (!is_array($this->validators)) {
            $this->validators = [];
            $this->optional();
        }
    }

    /**
     * @param string|array $rule
     * @param array|string $options ('')
     * @return Field
     */
    public function validator($rule, $options = null): Field
    {
        if (!is_array($rule)) {
            $this->validators[$rule] = $options;
            return $this;
        }
        foreach ($rule as $key => $value) {
            $name = $key;
            if (is_numeric($key)) {
                $name = $value;
                $value = '';
            }
            $this->validators[$name] = $value;
        }
        return $this;
    }

    /**
     * @param string $class
     * @param string $target
     * @return Field
     */
    public function referencedBy(string $class, string $target): Field
    {
        $this->referenced[$target] = $class;
        return $this;
    }

    /**
     * @param string $class
     * @param string $target
     * @return Field
     */
    public function referencesTo(string $class, string $target): Field
    {
        $this->references[$target] = $class;
        return $this;
    }

    /**
     * @return bool
     */
    public function isPrimaryKey(): bool
    {
        return $this->primaryKey;
    }

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
     * @param string $collection
     * @return Field
     */
    public function setCollection(string $collection): Field
    {
        $this->collection = $collection;
        return $this;
    }

    /**
     * @param string $name
     * @return Field
     */
    public function setName(string $name): Field
    {
        $this->name = $name;
        return $this;
    }

    /**
     * @param string $type
     * @return Field
     */
    public function setType(string $type): Field
    {
        $this->type = $type;
        return $this;
    }

    /**
     * @param array $options
     * @return Field
     */
    public function setOptions(array $options): Field
    {
        $this->options = $options;
        return $this;
    }

    /**
     * @param array $validators
     * @return Field
     */
    public function setValidators(array $validators): Field
    {
        $this->validators = $validators;
        return $this;
    }

    /**
     * @param string $label
     * @return Field
     */
    public function setLabel(string $label): Field
    {
        $this->label = $label;
        return $this;
    }

    /**
     * @param bool $create
     * @return Field
     */
    public function create(bool $create): Field
    {
        $this->create = $create;
        return $this;
    }

    /**
     * @param bool $read
     * @return Field
     */
    public function read(bool $read): Field
    {
        $this->read = $read;
        return $this;
    }

    /**
     * @param bool $update
     * @return Field
     */
    public function update(bool $update): Field
    {
        $this->update = $update;
        return $this;
    }

    /**
     * @param array $items
     * @return Field
     */
    public function enum(array $items): Field
    {
        $this->enum = $items;
        return $this;
    }

    /**
     * @param callable $callable
     */
    public function calculated(callable $callable)
    {
        $this->calculated = $callable;
    }

    /**
     * @return Field
     */
    public function required(): Field
    {
        $this->validator(['required', $this->type]);
        return $this;
    }

    /**
     * @return Field
     */
    public function optional(): Field
    {
        $this->validator([$this->type => ['optional' => true]]);
        return $this;
    }

    /**
     * @param $record
     * @return mixed
     */
    public function calculate($record)
    {
        $callable = $this->calculated;
        return $callable($record);
    }
}
