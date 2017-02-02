<?php

namespace Simples\Core\Data;

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
    const ACCEPTED = 'accepted';
    /**
     * @var string
     */
    const ACTIVE = 'active';
    /**
     * @var string
     */
    const AFTER = 'after';
    /**
     * @var string
     */
    const ALPHA = 'alpha';
    /**
     * @var string
     */
    const ALPHA_DASH = 'alpha-dash';
    /**
     * @var string
     */
    const ALPHA_NUMERIC = 'alpha-numeric';
    /**
     * @var string
     */
    const ARRAY = 'array';
    /**
     * @var string
     */
    const BEFORE = 'before';
    /**
     * @var string
     */
    const BETWEEN = 'between';
    /**
     * @var string
     */
    const BOOLEAN = 'boolean';
    /**
     * @var string
     */
    const CONFIRMED = 'confirmed';
    /**
     * @var string
     */
    const DATE = 'date';
    /**
     * @var string
     */
    const DATE_FORMAT = 'date-format';
    /**
     * @var string
     */
    const DIFFERENT = 'different';
    /**
     * @var string
     */
    const DIGITS = 'digits';
    /**
     * @var string
     */
    const DIGITS_BETWEEN = 'digits-between';
    /**
     * @var string
     */
    const DIMENSIONS = 'dimensions';
    /**
     * @var string
     */
    const DISTINCT = 'distinct';
    /**
     * @var string
     */
    const EMAIL = 'email';
    /**
     * @var string
     */
    const EXISTING = 'existing';
    /**
     * @var string
     */
    const FILE = 'file';
    /**
     * @var string
     */
    const FILLED = 'filled';
    /**
     * @var string
     */
    const IMAGE = 'image';
    /**
     * @var string
     */
    const IN = 'in';
    /**
     * @var string
     */
    const IN_ARRAY = 'in-array';
    /**
     * @var string
     */
    const FIELD = 'field';
    /**
     * @var string
     */
    const INTEGER = 'integer';
    /**
     * @var string
     */
    const IP = 'ip';
    /**
     * @var string
     */
    const JSON = 'json';
    /**
     * @var string
     */
    const MAX = 'max';
    /**
     * @var string
     */
    const MIMETYPES = 'mimetypes';
    /**
     * @var string
     */
    const MIMES = 'mimes';
    /**
     * @var string
     */
    const MIN = 'min';
    /**
     * @var string
     */
    const NULLABLE = 'nullable';
    /**
     * @var string
     */
    const NOT = 'not';
    /**
     * @var string
     */
    const NUMERIC = 'numeric';
    /**
     * @var string
     */
    const PRESENT = 'present';
    /**
     * @var string
     */
    const REGEX = 'regex';
    /**
     * @var string
     */
    const REQUIRED = 'required';
    /**
     * @var string
     */
    const REQUIRED_IF = 'required-if';
    /**
     * @var string
     */
    const SAME = 'same';
    /**
     * @var string
     */
    const SIZE = 'size';
    /**
     * @var string
     */
    const STRING = 'string';
    /**
     * @var string
     */
    const TIMEZONE = 'timezone';
    /**
     * @var string
     */
    const UNIQUE = 'unique';
    /**
     * @var string
     */
    const URL = 'url';

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
        //
        return $value;
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
     * @return mixed
     */
    public function isInteger($value)
    {
        //
        return $value;
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
     * @return mixed
     */
    public function isRequired($value)
    {
        return !empty($value);
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
     * @return mixed
     */
    public function isString($value)
    {
        //
        return $value;
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
            return $instance->fields($field)->read([$field => $value])->size() === 0;
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
            $problem = $this->apply($rule, $value, $options);
            if (!$problem) {
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
