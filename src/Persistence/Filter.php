<?php

namespace Simples\Core\Persistence;

use Simples\Core\Error\RunTimeError;
use Simples\Core\Helper\Json;
use Simples\Core\Kernel\App;
use Simples\Core\Model\Field;

/**
 * Class Filter
 * @package Simples\Core\Persistence
 */
class Filter
{
    /**
     * @var Field
     */
    private $field;

    /**
     * @var mixed
     */
    private $value;

    /**
     * @var string
     */
    private $rule;

    /**
     * @var bool
     */
    private $not;

    /**
     * @var string
     */
    const RULE_EQUAL = 'equal', RULE_NEAR = 'near', RULE_BETWEEN = 'between',
        RULE_DAY = 'day', RULE_MONTH = 'month', RULE_YEAR = 'year', RULE_COMPETENCE = 'competence',
        RULE_BLANK = 'blank';

    /**
     * Filter constructor.
     * @param Field $field
     * @param mixed $value
     * @param string $rule (null)
     * @param bool $not (false)
     */
    public function __construct(Field $field, $value, $rule = null, $not = false)
    {
        $this->field = $field;
        $this->value = $value;
        $this->not = $not;
        $this->parseRule($rule);
    }

    /**
     * @return string
     */
    public function getCollection(): string
    {
        return $this->field->getCollection();
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->field->getName();
    }

    /**
     * @return string
     */
    public function getType(): string
    {
        return $this->field->getType();
    }

    /**
     * @return mixed
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * @return string
     */
    public function getRule(): string
    {
        return $this->rule;
    }

    /**
     * @return bool
     */
    public function isNot(): bool
    {
        return $this->not;
    }

    /**
     * @param $rule
     * @return bool
     */
    private function ruleExists($rule): bool
    {
        return defined('static::RULE_' . strtoupper($rule));
    }

    /**
     * @param mixed $value
     * @param string $rule
     * @return string
     */
    public static function rule($value, $rule = null): string
    {
        $marker = App::options('filter');
        if (!is_scalar($value)) {
            $value = Json::encode($value);
        }
        if (!$rule) {
            $rule = static::RULE_EQUAL;
        }
        return "{$rule}{$marker}{$value}";
    }

    /**
     * @return mixed
     */
    public function getParsedValue()
    {
        switch ($this->rule) {
            case static::RULE_BETWEEN:
            case static::RULE_COMPETENCE: {
                $separator = ',';
                $size = 2;
                if ($this->rule === static::RULE_COMPETENCE) {
                    $separator = '/';
                }
                return $this->separator($this->value, $separator, $size);
            }
            case static::RULE_NEAR: {
                $value = $this->value;
                if (!is_scalar($value)) {
                    $value = Json::encode($value);
                }
                return "%{$value}%";
            }
        }
        return $this->value;
    }

    /**
     * @param string $value
     * @param string $separator
     * @param int $size
     * @return array
     * @throws RunTimeError
     */
    protected function separator(string $value, string $separator, int $size): array
    {
        $array = explode($separator, $value);
        if (count($array) < $size) {
            $count = count($array);
            throw new RunTimeError("Invalid number of arguments to create a rule. " .
                "Expected '{$size}' given '{$count}' to rule '{$this->rule}'");
        }
        if (count($array) > $size) {
            $array = array_slice($array, 0, $size);
        }
        return $array;
    }

    /**
     * @param $rule
     */
    private function parseRule($rule)
    {
        if (!$rule) {
            $this->rule = static::RULE_EQUAL;
            $peaces = explode(App::options('filter'), $this->value);
            $filter = (string)$peaces[0];
            if ($filter{0} === '!') {
                $filter = substr($filter, 1);
                $this->not = true;
            }
            if ($this->ruleExists($filter)) {
                $this->rule = $filter;
                array_shift($peaces);
                $this->value = implode(App::options('filter'), $peaces);
            }
        }
    }
}
