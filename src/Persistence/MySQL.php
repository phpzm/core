<?php

namespace Simples\Core\Persistence;

/**
 * Class MySQL
 * @package Simples\Core\Persistence
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