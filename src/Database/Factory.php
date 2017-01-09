<?php

namespace Simples\Core\Database;

/**
 * Class Factory
 * @package Simples\Core\Database
 */
class Factory
{
    /**
     * @param $options
     * @return MySQL
     * @throws \Exception
     */
    public static function create($options)
    {
        $platform = strtolower($options['platform']);
        switch ($platform) {
            case 'mysql':
                return new MySQL($options);
        }
        throw new \Exception('There is no driver to "' . $platform . '"');
    }
}