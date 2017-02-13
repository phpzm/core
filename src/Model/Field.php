<?php

namespace Simples\Core\Model;
use Simples\Core\Error\RunTimeError;

/**
 * @method Field string($size = 255)
 * @method Field text()
 * @method Field datetime($format = 'Y-m-d H:i:s')
 * @method Field date($format = 'Y-m-d')
 * @method Field integer($size = 10)
 * @method Field float($size = 10, $decimal = 4)
 * @method Field file()
 * @method Field array()
 * @method Field boolean()
 * Class Field
 * @package Simples\Core\Model
 */
class Field extends FieldContract
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
     * @var array
     */
    private $supported = ['string', 'text', 'datetime', 'date', 'integer', 'float', 'file', 'array', 'boolean'];

    /**
     * Field constructor.
     * @param string $collection
     * @param string $name
     * @param string $type
     * @param array $options
     */
    public function __construct(string $collection, string $name, string $type = '', array $options = [])
    {
        $this->collection = $collection;
        $this->name = $name;
        $this->type = $type ? $type : Field::TYPE_STRING;
        $this->options = $options;

        $default = [
            'primaryKey' => false, 'label' => '', 'validators' => [], 'create' => true, 'read' => true, 'update' => true,
            'enum' => [], 'referenced' => [], 'references' => [],
        ];
        $options = array_merge($default, $options);

        foreach ($options as $key => $value) {
            /** @noinspection PhpVariableVariableInspection */
            $this->$key = $value;
        }
        if (off($options, 'primaryKey')) {
            $this->create(false);
            $this->update(false);
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
     * @param array $items
     * @return Field
     */
    public function enum(array $items): Field
    {
        $this->string();
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

    /**
     * @param string $reference
     * @return Field
     */
    public function from(string $reference): Field
    {
        $this->collection = '';
        $this->from = $reference;
        $this->create(false)->read(true)->update(false);
        return $this;
    }

    /**
     * @param $name
     * @param $arguments
     * @return Field
     * @throws RunTimeError
     */
    public function __call($name, $arguments): Field
    {
        if (in_array($name, $this->supported)) {
            $this->type = $name;
            if (!$this->validators) {
                $this->optional();
            }
            return $this;
        }
        throw new RunTimeError("Type '{$name}' not supported");
    }
}
