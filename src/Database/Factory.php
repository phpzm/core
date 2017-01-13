<?php

namespace Simples\Core\Database;

/**
 * Class Factory
 * @package Simples\Core\Database
 */
class Factory
{
    /**
     * @param $settings
     * @param string $hashKey
     * @param string $deletedKey
     * @param array $timestampsKeys
     * @return null
     */
    public static function create($settings, $hashKey = '', $deletedKey = '', array $timestampsKeys = [])
    {
        $driver = $settings['driver'];
        if (class_exists($driver)) {
            return new $driver($settings, $hashKey, $deletedKey, $timestampsKeys);
        }
        return null;
    }
}