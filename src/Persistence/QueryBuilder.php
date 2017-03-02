<?php

namespace Simples\Core\Persistence;

/**
 * Class QueryBuilder
 * @package Simples\Core\Persistence
 */
class QueryBuilder extends Engine
{
    /**
     * Defines which connection will be used
     * @var string
     */
    protected $connection = 'default';

    /**
     * QueryBuilder constructor.
     */
    public function __construct()
    {
        parent::__construct($this->connection);
    }
}
