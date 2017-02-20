<?php

namespace Simples\Core\Data\Validators;

use Simples\Core\Kernel\Container;
use Simples\Core\Model\AbstractModel;
use Simples\Core\Persistence\Filter;

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
     * @return bool
     */
    public function isReject ($value): bool
    {
        return empty($value);
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
            $instance = Container::box()->make($class);
            /** @var AbstractModel $instance */
            $filter = [$field => $value];
            if (off($options, 'primaryKey.value')) {
                $filter[off($options, 'primaryKey.name')] = Filter::apply(Filter::RULE_NOT, off($options, 'primaryKey.value'));
            }
            return $instance->count($filter) === 0;
        }
        return false;
    }
}
