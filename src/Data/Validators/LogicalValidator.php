<?php

namespace Simples\Core\Data\Validators;

/**
 * Class LogicalValidator
 * @package Simples\Core\Data\Validators
 */
trait LogicalValidator
{
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
        return $value;
    }

    /**
     * @param $value
     * @return mixed
     */
    public function isConfirmed($value)
    {
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
    public function isNot($value)
    {
        // -in:[foobar(...)]
        return $value;
    }

    /**
     * @param $value
     * @return mixed
     */
    public function isPresent($value)
    {
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
}
