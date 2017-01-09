<?php

namespace Simples\Core\Database;

/**
 * Class MySQL
 * @package Simples\Core\Database
 */
class MySQL extends Driver
{
    /**
     * @return string
     */
    protected function dsn()
    {
        return "mysql:host={$this->options['host']};port={$this->options['port']};dbname={$this->options['database']}";
    }
}