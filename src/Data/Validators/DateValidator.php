<?php

namespace Simples\Core\Data\Validators;

use Simples\Core\Helper\Date;

/**
 * Class DateValidator
 * @package Simples\Core\Data\Validators
 */
trait DateValidator
{
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
    public function isTimezone($value)
    {
        return $value;
    }
}
