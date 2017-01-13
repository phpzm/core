<?php

namespace Simples\Core\Data;
use Simples\Core\Kernel\Lang;
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
    const IS_ACCEPTED = 'isAccepted';
    /**
     * @var string
     */
    const IS_ACTIVE = 'isActive';
    /**
     * @var string
     */
    const IS_AFTER = 'isAfter';
    /**
     * @var string
     */
    const IS_ALPHA = 'isAlpha';
    /**
     * @var string
     */
    const IS_ALPHA_DASH = 'isAlphaDash';
    /**
     * @var string
     */
    const IS_ALPHA_NUMERIC = 'isAlphaNumeric';
    /**
     * @var string
     */
    const IS_ARRAY = 'isArray';
    /**
     * @var string
     */
    const IS_BEFORE = 'isBefore';
    /**
     * @var string
     */
    const IS_BETWEEN = 'isBetween';
    /**
     * @var string
     */
    const IS_BOOLEAN = 'isBoolean';
    /**
     * @var string
     */
    const IS_CONFIRMED = 'isConfirmed';
    /**
     * @var string
     */
    const IS_DATE = 'isDate';
    /**
     * @var string
     */
    const IS_DATE_FORMAT = 'isDateFormat';
    /**
     * @var string
     */
    const IS_DIFFERENT = 'isDifferent';
    /**
     * @var string
     */
    const IS_DIGITS = 'isDigits';
    /**
     * @var string
     */
    const IS_DIGITS_BETWEEN = 'isDigitsBetween';
    /**
     * @var string
     */
    const IS_DIMENSIONS = 'isDimensions';
    /**
     * @var string
     */
    const IS_DISTINCT = 'isDistinct';
    /**
     * @var string
     */
    const IS_EMAIL = 'isEmail';
    /**
     * @var string
     */
    const IS_EXISTING = 'isExisting';
    /**
     * @var string
     */
    const IS_FILE = 'isFile';
    /**
     * @var string
     */
    const IS_FILLED = 'isFilled';
    /**
     * @var string
     */
    const IS_IMAGE = 'isImage';
    /**
     * @var string
     */
    const IS_IN = 'isIn';
    /**
     * @var string
     */
    const IS_IN_ARRAY = 'isInArray';
    /**
     * @var string
     */
    const IS_FIELD = 'isField';
    /**
     * @var string
     */
    const IS_INTEGER = 'isInteger';
    /**
     * @var string
     */
    const IS_IP = 'isIp';
    /**
     * @var string
     */
    const IS_JSON = 'isJson';
    /**
     * @var string
     */
    const IS_MAX = 'isMax';
    /**
     * @var string
     */
    const IS_MIMETYPES = 'isMimetypes';
    /**
     * @var string
     */
    const IS_MIMES = 'isMimes';
    /**
     * @var string
     */
    const IS_MIN = 'isMin';
    /**
     * @var string
     */
    const IS_NULLABLE = 'isNullable';
    /**
     * @var string
     */
    const IS_NOT = 'isNot';
    /**
     * @var string
     */
    const IS_NUMERIC = 'isNumeric';
    /**
     * @var string
     */
    const IS_PRESENT = 'isPresent';
    /**
     * @var string
     */
    const IS_REGEX = 'isRegex';
    /**
     * @var string
     */
    const IS_REQUIRED = 'isRequired';
    /**
     * @var string
     */
    const IS_REQUIRED_IF = 'isRequiredIf';
    /**
     * @var string
     */
    const IS_SAME = 'isSame';
    /**
     * @var string
     */
    const IS_SIZE = 'isSize';
    /**
     * @var string
     */
    const IS_STRING = 'isString';
    /**
     * @var string
     */
    const IS_TIMEZONE = 'isTimezone';
    /**
     * @var string
     */
    const IS_UNIQUE = 'isUnique';
    /**
     * @var string
     */
    const IS_URL = 'isUrl';

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
        //
        return $value;
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
        return empty($value);
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
     * @return mixed
     */
    public function isUnique($value)
    {
        // :[field(...)]
        return $value;
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
     * @return bool
     */
    public function apply($rule, $value)
    {
        $method = (string)Stringy::create("is-{$rule}")->camelize();
        if (method_exists($this, $method)) {
            return $this->$method($value);
        }
        return false;
    }

    /**
     * @param array $rules
     * @return Record
     */
    public function parse(array $rules): Record
    {
        $errors = [];
        foreach ($rules as $attribute => $options) {
            $keys = array_keys($options);
            foreach ($keys as $rule) {
                if ($this->apply($rule, $options[$rule])) {
                    $errors[$attribute] = $rule;
                }
            }
        }
        return new Record($errors);
    }

}