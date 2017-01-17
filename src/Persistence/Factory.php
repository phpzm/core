<?php

namespace Simples\Core\Persistence;

/**
 * Class Factory
 * @package Simples\Core\Persistence
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
            $connection = Transaction::recover($driver);
            if ($connection) {
                return $connection;
            }
            return Transaction::register($driver, new $driver($settings, $hashKey, $deletedKey, $timestampsKeys));
        }
        return null;
    }
}
