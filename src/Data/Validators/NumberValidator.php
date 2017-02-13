<?php

namespace Simples\Core\Data\Validators;

/**
 * Class NumberValidator
 * @package Simples\Core\Data\Validators
 */
trait NumberValidator
{
    /**
     * @param $value
     * @return mixed
     */
    public function isDigits($value)
    {
        return ctype_digit($value);
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
     * @return bool
     */
    public function isFloat($value): bool
    {
        if ($value === "") {
            return false;
        }
        if (gettype($value) === TYPE_STRING) {
            $value = (float)$value;
        }
        return is_float($value);
    }

    /**
     * @param $value
     * @return bool
     */
    public function isInteger($value)
    {
        if ($value === "") {
            return false;
        }
        if (gettype($value) === TYPE_STRING) {
            $value = (integer)$value;
        }
        return is_integer($value);
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
    public function isMax($value)
    {
        // :value
        return $value;
    }

    /**
     * @param $value
     * @return mixed
     */
    public function isNumeric($value)
    {
        return is_numeric($value);
    }
}
