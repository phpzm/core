<?php

namespace Simples\Core\Data\Validators;

/**
 * Class StringValidator
 * @package Simples\Core\Data\Validators
 */
trait StringValidator
{
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
    public function isUrl($value)
    {
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
    public function isIp($value)
    {
        return $value;
    }

    /**
     * @param $value
     * @return mixed
     */
    public function isJson($value)
    {
        return $value;
    }
}
