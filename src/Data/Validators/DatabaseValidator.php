<?php

namespace Simples\Core\Data\Validators;

use Simples\Core\Kernel\Container;
use Simples\Core\Model\AbstractModel;

/**
 * Class DateValidator
 * @package Simples\Core\Data\Validators
 */
trait DatabaseValidator
{
    /**
     * @param $value
     * @return mixed
     */
    public function isField($value)
    {
        return $value;
    }

    /**
     * @param $value
     * @return mixed
     */
    public function isNullable($value)
    {
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

}
