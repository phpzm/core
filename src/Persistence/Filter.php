<?php

namespace Simples\Core\Persistence;

use Simples\Core\Helper\Json;
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
     * @var string
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
    const MARKER = '~>';

    /**
     * @var string
     */
    const RULE_EQUAL = 'equal', RULE_NEAR = 'near', RULE_BETWEEN = 'between',
        RULE_DAY = 'day', RULE_MONTH = 'month', RULE_YEAR = 'year',
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
        if (!$rule) {
            $rule = static::RULE_EQUAL;
            $peaces = explode(static::MARKER, $value);
            $filter = $peaces[0];
            if (substr($filter, 0, 1) === '!') {
                $filter = substr($filter, 1);
                $not = true;
            }
            if ($this->ruleExists($filter)) {
                array_shift($peaces);
                $rule = $filter;
                $this->value = implode(static::MARKER, $peaces);
            }
        }
        $this->rule = $rule;
        $this->not = $not;
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
     * @return string
     */
    public function getValue(): string
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
        $marker = static::MARKER;
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
                return explode(',', $this->value);
                break;
        }
        return $this->value;
    }
}
