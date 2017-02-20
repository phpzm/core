<?php

namespace Simples\Core\Model;

use Simples\Core\Data\Record;
use Simples\Core\Error\SimplesRunTimeError;

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
class Field extends AbstractField
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
            'primaryKey' => false, 'label' => '', 'validators' => [],
            'create' => true, 'read' => true, 'update' => true, 'recover' => true, 'readonly' => false,
            'enum' => [], 'referenced' => [], 'references' => (object)[], 'default' => ''
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
            $this->optional();
        }
    }

    /**
     * @SuppressWarnings("BooleanArgumentFlag")
     *
     * @param string|array $rule
     * @param array|string $options ('')
     * @param bool $clear
     * @return Field
     */
    public function validator($rule, $options = null, bool $clear = false): Field
    {
        if ($clear) {
            $this->validators = [];
        }
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
     * @SuppressWarnings("BooleanArgumentFlag")
     *
     * @param string $class
     * @param string $referenced
     * @param bool $nullable
     * @return Field
     * @throws SimplesRunTimeError
     */
    public function referencesTo(string $class, string $referenced, bool $nullable = false): Field
    {
        if (off($this->references, 'class')) {
            throw new SimplesRunTimeError("Relationship already defined to '{$this->references->class}'");
        }
        $this->references = (object)[
            'collection' => $this->getCollection(),
            'referenced' => $referenced,
            'class' => $class
        ];
        if ($nullable) {
            $this->default = null;
        }
        return $this;
    }

    /**
     * @param array $items
     * @return Field
     */
    public function enum(array $items): Field
    {
        if (!$this->type) {
            $this->string();
        }
        $this->enum = $items;
        return $this;
    }

    /**
     * @return Field
     */
    public function nullable(): Field
    {
        $this->default = null;
        return $this;
    }

    /**
     * @param callable $callable
     * @return Field
     */
    public function calculated(callable $callable): Field
    {
        $this->calculated = $callable;
        return $this;
    }

    /**
     * @SuppressWarnings("BooleanArgumentFlag")
     *
     * @param array $rules
     * @return Field
     * @internal param bool $force
     */
    public function required(array $rules = []): Field
    {
        $this->validator(['required', $this->type], null, true);
        foreach ($rules as $rule) {
            $this->validator($rule, ['optional' => true], false);
        }
        return $this;
    }

    /**
     * @param array $rules
     * @return Field
     */
    public function optional(array $rules = []): Field
    {
        $this->validator($this->type, ['optional' => true], true);
        foreach ($rules as $rule) {
            $this->validator($rule, ['optional' => true], false);
        }
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
     * @param Field $reference
     * @return Field
     */
    public function from(Field $reference): Field
    {
        $this->from = $reference;
        return $this->create(false)->read(true)->update(false);
    }

    /**
     * @param $name
     * @param $arguments
     * @return Field
     * @throws SimplesRunTimeError
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
        throw new SimplesRunTimeError("Type '{$name}' not supported");
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
    public function readonly()
    {
        $this->readonly = true;
        return $this->create(false)->read(false)->update(false);
    }

    /**
     * @return Field
     */
    public function reject(): Field
    {
        $this->validators = ['reject' => ''];
        $this->enum = [];
        return $this;
    }

    /**
     * @return Field
     */
    public function primaryKey(): Field
    {
        $this->primaryKey = true;
        return $this->integer()->recover(false);
    }

    /**
     * @return Field
     */
    public function hashKey(): Field
    {
        return $this->optional(['unique'])->update(false);
    }
}
