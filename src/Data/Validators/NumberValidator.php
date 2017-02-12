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
     * @return bool
     */
    public function isFloat($value): bool
    {
        return is_numeric($value);
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
        return $value;
    }
}
