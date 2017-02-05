<?php

namespace Simples\Core\Persistence\SQL;

use Simples\Core\Persistence\Filter;
use ErrorException;
use Simples\Core\Route\Wrapper;

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
    protected function equal(string $name): string
    {
        return "{$name} = ?";
    }

    /**
     * @param string $name
     * @return string
     */
    protected function between($name): string
    {
        return "{$name} BETWEEN ? AND ?";
    }

    /**
     * @param string $name
     * @return string
     */
    protected function near($name): string
    {
        return "{$name} LIKE ?";
    }

    /**
     * @param string $name
     * @return string
     */
    protected function day($name): string
    {
        return "DAY({$name}) = ?";
    }

    /**
     * @param string $name
     * @return string
     */
    protected function month($name): string
    {
        return "MONTH({$name}) = ?";
    }

    /**
     * @param string $name
     * @return string
     */
    protected function year($name): string
    {
        return "YEAR({$name}) = ?";
    }

    /**
     * @param string $name
     * @return string
     */
    protected function competence($name): string
    {
        return "MONTH({$name}) = ? AND YEAR({$name}) = ?";
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