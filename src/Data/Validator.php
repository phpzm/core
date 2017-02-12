<?php

namespace Simples\Core\Data;

use Simples\Core\Helper\Date;
use Simples\Core\Kernel\Container;
use Simples\Core\Model\AbstractModel;
use Stringy\Stringy;

/**
 * Class Validator
 * @package Simples\Core\Data
 */
class Validator
{
    /**
     * @var string
     */
    const ACCEPTED = 'accepted',  ACTIVE = 'active', AFTER = 'after', ALPHA = 'alpha', ALPHA_DASH = 'alpha-dash',
        ALPHA_NUMERIC = 'alpha-numeric', ARRAY = 'array', BEFORE = 'before', BETWEEN = 'between', BOOLEAN = 'boolean',
        CONFIRMED = 'confirmed', DATE = 'date', DATE_FORMAT = 'date-format', DIFFERENT = 'different', DIGITS = 'digits',
        DIGITS_BETWEEN = 'digits-between', DIMENSIONS = 'dimensions', DISTINCT = 'distinct', EMAIL = 'email',
        EXISTING = 'existing', FILE = 'file', FILLED = 'filled', IMAGE = 'image', IN = 'in', IN_ARRAY = 'in-array',
        FIELD = 'field', INTEGER = 'integer', IP = 'ip', JSON = 'json', MAX = 'max', MIMETYPES = 'mimetypes',
        MIMES = 'mimes', MIN = 'min', NULLABLE = 'nullable', NOT = 'not', NUMERIC = 'numeric', PRESENT = 'present',
        REGEX = 'regex', REQUIRED = 'required', REQUIRED_IF = 'required-if', SAME = 'same', SIZE = 'size',
        STRING = 'string', TIMEZONE = 'timezone', UNIQUE = 'unique', URL = 'url';

    /**
     * @param $criteria
     * @param $value
     * @return array
     */
    public static function rule($criteria, $value): array
    {
        if (!is_array($criteria)) {
            $criteria = explode(',', $criteria);
        }
        $rules = [];
        foreach ($criteria as $key => $options) {
            if (is_numeric($key)) {
                $key = $options;
                $options = '';
            }
            $rules[$key] = $options;
        }
        return ['rules' => $rules, 'value' => $value];
    }

    /**
     * @param $value
     * @return mixed
     */
    public function isAccepted($value)
    {
        // 	return $value
        return $value;
    }

    /**
     * @param $value
     * @return mixed
     */
    public function isActive($value)
    {
        // -url
        return $value;
    }

    /**
     * @param $value
     * @return mixed
     */
    public function isAfter($value)
    {
        // :date
        return $value;
    }

    /**
     * @param $value
     * @return mixed
     */
    public function isAlpha($value)
    {
        //
        return $value;
    }

    /**
     * @param $value
     * @return mixed
     */
    public function isAlphaDash($value)
    {
        // -dash
        return $value;
    }

    /**
     * @param $value
     * @return mixed
     */
    public function isAlphaNumeric($value)
    {
        // -numeric
        return $value;
    }

    /**
     * @param $value
     * @return mixed
     */
    public function isArray($value)
    {
        //
        return $value;
    }

    /**
     * @param $value
     * @return mixed
     */
    public function isBefore($value)
    {
        // :date
        return $value;
    }

    /**
     * @param $value
     * @return mixed
     */
    public function isBetween($value)
    {
        // :min, max
        return $value;
    }

    /**
     * @param $value
     * @return mixed
     */
    public function isBoolean($value)
    {
        //
        return $value;
    }

    /**
     * @param $value
     * @return mixed
     */
    public function isConfirmed($value)
    {
        //
        return $value;
    }

    /**
     * @param $value
     * @return mixed
     */
    public function isDate($value)
    {
        return Date::isDate($value);
    }

    /**
     * @param $value
     * @return mixed
     */
    public function isDateFormat($value)
    {
        // :format
        return $value;
    }

    /**
     * @param $value
     * @return mixed
     */
    public function isDifferent($value)
    {
        // :field
        return $value;
    }

    /**
     * @param $value
     * @return mixed
     */
    public function isDigits($value)
    {
        // :value
        return $value;
    }

    /**
     * @param $value
     * @return mixed
     */
    public function isDigitsBetween($value)
    {
        // :min, max
        return $value;
    }

    /**
     * @param $value
     * @return mixed
     */
    public function isDimensions($value)
    {
        // :width, height
        return $value;
    }

    /**
     * @param $value
     * @return mixed
     */
    public function isDistinct($value)
    {
        //
        return $value;
    }

    /**
     * @param $value
     * @return mixed
     */
    public function isEmail($value)
    {
        return filter_var($value, FILTER_VALIDATE_EMAIL);
    }

    /**
     * @param $value
     * @return mixed
     */
    public function isExisting($value)
    {
        // :class.[property(...)]
        return $value;
    }

    /**
     * @param $value
     * @return mixed
     */
    public function isFile($value)
    {
        //
        return $value;
    }

    /**
     * @param $value
     * @return mixed
     */
    public function isFilled($value)
    {
        //
        return $value;
    }

    /**
     * @param $value
     * @return bool
     */
    public function isFloat($value): bool
    {
        return is_numeric($value);
    }

    /**
     * @param $value
     * @return mixed
     */
    public function isImage($value)
    {
        //
        return $value;
    }

    /**
     * @param $value
     * @return mixed
     */
    public function isIn($value)
    {
        // :[foobar(...)]
        return $value;
    }

    /**
     * @param $value
     * @return mixed
     */
    public function isInArray($value)
    {
        //
        return $value;
    }

    /**
     * @param $value
     * @return mixed
     */
    public function isField($value)
    {
        //
        return $value;
    }

    /**
     * @param $value
     * @return bool
     */
    public function isInteger($value)
    {
        return is_numeric((int)$value);
    }

    /**
     * @param $value
     * @return mixed
     */
    public function isIp($value)
    {
        //
        return $value;
    }

    /**
     * @param $value
     * @return mixed
     */
    public function isJson($value)
    {
        //
        return $value;
    }

    /**
     * @param $value
     * @return mixed
     */
    public function isMax($value)
    {
        // :value
        return $value;
    }

    /**
     * @param $value
     * @return mixed
     */
    public function isMimetypes($value)
    {
        // :[text/plain(...)]
        return $value;
    }

    /**
     * @param $value
     * @return mixed
     */
    public function isMimes($value)
    {
        // :[jpeg,png,bmp,gif,svg(...)]
        return $value;
    }

    /**
     * @param $value
     * @return mixed
     */
    public function isMin($value)
    {
        // :value
        return $value;
    }

    /**
     * @param $value
     * @return mixed
     */
    public function isNullable($value)
    {
        //
        return $value;
    }

    /**
     * @param $value
     * @return mixed
     */
    public function isNot($value)
    {
        // -in:[foobar(...)]
        return $value;
    }

    /**
     * @param $value
     * @return mixed
     */
    public function isNumeric($value)
    {
        //
        return $value;
    }

    /**
     * @param $value
     * @return mixed
     */
    public function isPresent($value)
    {
        //
        return $value;
    }

    /**
     * @param $value
     * @return mixed
     */
    public function isRegex($value)
    {
        // :pattern
        return $value;
    }

    /**
     * @param $value
     * @param array $options
     * @return bool
     */
    public function isRequired($value, $options = []): bool
    {
        if (off($options, 'enum')) {
            return in_array($value, off($options, 'enum'));
        }
        return !empty((string)$value);
    }

    /**
     * @param $value
     * @return mixed
     */
    public function isRequiredIf($value)
    {
        // -if
        return $value;
    }

    /**
     * @param $value
     * @return mixed
     */
    public function isSame($value)
    {
        // :field
        return $value;
    }

    /**
     * @param $value
     * @return mixed
     */
    public function isSize($value)
    {
        // :value
        return $value;
    }

    /**
     * @param $value
     * @return bool
     */
    public function isString($value): bool
    {
        return !!strlen($value);
    }

    /**
     * @param $value
     * @return bool
     */
    public function isText($value)
    {
        return $this->isString($value);
    }

    /**
     * @param $value
     * @return mixed
     */
    public function isTimezone($value)
    {
        //
        return $value;
    }

    /**
     * @param $value
     * @param $options
     * @return mixed
     */
    public function isUnique($value, $options)
    {
        $class = off($options, 'class');
        $field = off($options, 'field');
        if (class_exists($class)) {
            $instance = Container::getInstance()->make($class);
            /** @var AbstractModel $instance */
            return $instance->count([$field => $value]) === 0;
        }
        return false;
    }

    /**
     * @param $value
     * @return mixed
     */
    public function isUrl($value)
    {
        //
        return $value;
    }

    /**
     * @param $rule
     * @param $value
     * @param $options
     * @return bool
     */
    public function apply($rule, $value, $options)
    {
        $method = (string)Stringy::create("is-{$rule}")->camelize();
        if (method_exists($this, $method)) {
            return $this->$method($value, $options);
        }
        return false;
    }

    /**
     * @param $rules
     * @param $value
     * @return array
     */
    public function applyRules($rules, $value)
    {
        $error = [];
        foreach ($rules as $rule => $options) {
            if (!$value && off($options, 'optional')) {
                continue;
            }
            $isValid = $this->apply($rule, $value, $options);
            if (!$isValid) {
                $error[] = $rule;
            }
        }
        return $error;
    }

    /**
     * @param array $validators
     * @return Record
     * @throws \ErrorException
     */
    public function parse(array $validators): Record
    {
        $errors = [];
        // Wrapper::info($validators);
        foreach ($validators as $field => $settings) {
            $error = $this->applyRules(off($settings, 'rules'), off($settings, 'value'));
            if (count($error)) {
                $errors[$field] = $error;
            }
        }
        return new Record($errors);
    }
}
