<?php

namespace Simples\Core\Model;

use Simples\Core\Error\SimplesRunTimeError;
use stdClass;

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
 *
 * @method Field collection($collection)
 * @method Field name($name)
 * @method Field type($type)
 * @method Field label($label)
 * @method Field alias($alias)
 * @method Field create($create)
 * @method Field read($read)
 * @method Field update($update)
 * @method Field recover($recover)
 *
 * @method string getCollection()
 * @method Field setCollection($collection)
 * @method string getName()
 * @method Field setName($name)
 * @method string getType()
 * @method Field setType($type)
 * @method string getLabel()
 * @method Field setLabel($label)
 * @method string getAlias()
 * @method Field setAlias($alias)
 * @method bool isPrimaryKey()
 * @method Field setPrimaryKey($primaryKey)
 * @method bool isCreate()
 * @method Field setCreate($create)
 * @method bool isRead()
 * @method Field setRead($read)
 * @method bool isUpdate()
 * @method Field setUpdate($update)
 * @method bool isRecover()
 * @method Field setRecover($recover)
 *
 * @method Field default($default)
 *
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
     * @var array
     */
    private $supported = ['string', 'text', 'datetime', 'date', 'integer', 'float', 'file', 'array', 'boolean'];

    /**
     * @var array
     */
    private $options = [];

    /**
     * @var array
     */
    protected $validators;

    /**
     * @var array
     */
    protected $enum;

    /**
     * @var array
     */
    protected $referenced;

    /**
     * @var stdClass
     */
    protected $references;

    /**
     * @var Field
     */
    protected $from;

    /**
     * @var callable
     */
    protected $calculated;

    /**
     * @var callable
     */
    protected $map;

    /**
     * Field constructor.
     * @param string $collection
     * @param string $name
     * @param string $type (null)
     * @param array $options ([])
     */
    public function __construct(string $collection, string $name, string $type = null, array $options = [])
    {
        $default = [
            'collection' => $collection, 'name' => $name, 'type' => $type ?? Field::TYPE_STRING,
            'primaryKey' => false, 'label' => '', 'default' => '', 'alias' => '',
            'create' => true, 'read' => true, 'update' => true, 'recover' => true
        ];
        $this->options = array_merge($default, $options);

        if (off($this->options, 'primaryKey')) {
            $this->create(false);
            $this->update(false);
        }
        if (!is_array($this->validators)) {
            $this->optional();
        }

        $this->validators = [];
        $this->enum = [];
        $this->referenced = [];
        $this->references = (object)[];
    }

    /**
     * @param $name
     * @param $arguments
     * @return mixed
     * @throws SimplesRunTimeError
     */
    public function __call($name, $arguments)
    {
        if (in_array($name, array_keys($this->options), true) && isset($arguments[0])) {
            $this->options[$name] = $arguments[0];
            return $this;
        }
        if (substr($name, 0, 3) === 'get') {
            return $this->option(lcfirst(substr($name, 3)));
        }
        if (substr($name, 0, 2) === 'is') {
            return $this->option(lcfirst(substr($name, 2)));
        }
        if (substr($name, 0, 3) === 'set' && isset($arguments[0])) {
            $this->option(lcfirst(substr($name, 3)), $arguments[0]);
            return $this;
        }
        if (in_array($name, $this->supported, true)) {
            $this->option('type', $name);
            if (!$this->validators) {
                $this->optional();
            }
            return $this;
        }
        throw new SimplesRunTimeError("Type '{$name}' not supported");
    }

    /**
     * @param string $key
     * @param mixed $value (null)
     * @return mixed
     */
    public function option(string $key, $value = null)
    {
        if ($value) {
            $this->options[$key] = $value;
        }
        return off($this->options, $key);
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
            $this->option('default', null);
        }
        return $this;
    }

    /**
     * @param array $items
     * @return Field
     */
    public function enum(array $items): Field
    {
        if (!$this->option('type')) {
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
        $this->option('default', null);
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
        $this->validator(['required', $this->option('type')], null, true);
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
        $this->validator($this->option('type'), ['optional' => true], true);
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
        $this->option('readonly', true);
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
        $this->option('primaryKey', true);
        return $this->integer()->recover(false);
    }

    /**
     * @return Field
     */
    public function hashKey(): Field
    {
        return $this->optional(['unique'])->update(false);
    }

    /**
     * @return mixed
     */
    public function getDefault()
    {
        if (is_callable($this->option('default'))) {
            $callable = $this->option('default');
            return $callable();
        }
        return $this->option('default');
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
    public function getOptions(): array
    {
        return $this->options;
    }

    /**
     * @return array
     */
    public function getEnum(): array
    {
        return $this->enum;
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
     * @return Field
     */
    public function getFrom(): Field
    {
        return $this->from;
    }

    /**
     * @return callable
     */
    public function getCalculated(): callable
    {
        return $this->calculated;
    }

    /**
     * @return callable
     */
    public function getMap(): callable
    {
        return $this->map;
    }
}
