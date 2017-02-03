<?php

namespace Simples\Core\Persistence\SQL;

use Simples\Core\Persistence\Filter;
use ErrorException;

/**
 * Class SQLFilterSolver
 * @package Simples\Core\Persistence\SQL
 */
class SQLFilterSolver
{
    /**
     * @param Filter $filter
     * @return string
     * @throws ErrorException
     */
    public function render(Filter $filter): string
    {
        $rule = $filter->getRule();
        if (method_exists($this, $rule)) {
            $name = "{$filter->getCollection()}.{$filter->getName()}";
            $value = $filter->getValue();
            $not = $filter->isNot() ? 'NOT ' : '';
            return "{$not}(" . $this->$rule($name, $value) . ")";
        }
        throw new ErrorException("SQLFilterSolver can't resolve '{$rule}'");
    }

    /**
     * @param string $name
     * @return string
     */
    protected function equal(string $name)
    {
        return "{$name} = ?";
    }

    /**
     * @param string $name
     * @return string
     */
    protected function between($name)
    {
        return "{$name} BETWEEN ? AND ?";
    }

    /**
     * @param string $name
     * @return string
     */
    protected function month($name)
    {
        return "MONTH({$name}) = ?";
    }

    /**
     * @param string $name
     * @return string
     */
    protected function blank(string $name): string
    {
        return "({$name} IS NULL) OR (NOT {$name})";
    }
}