<?php

namespace Simples\Core\Database;

/**
 * Class MySQL
 * @package Simples\Core\Database
 */
class MySQL extends SQLDriver
{
    /**
     * @return string
     */
    protected function dsn()
    {
        return "mysql:host={$this->settings['host']};port={$this->settings['port']};dbname={$this->settings['database']}";
    }
}