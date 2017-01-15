<?php

namespace Simples\Core\Persistence;

/**
 * Class Transaction
 * @package Simples\Core\Persistence
 */
class Transaction
{
    /**
     * @var array
     */
    private static $connections = [];

    /**
     * @param $driver
     * @param Driver $connection
     */
    public static function register($driver, Driver $connection)
    {
        $connection->start();

        self::$connections[$driver] = $connection;
    }

    /**
     * @return bool
     */
    public static function commit()
    {
        foreach (self::$connections as $connection) {
            /** @var Driver $connection */
            if (!$connection->commit()) {
                self::rollback();
                return false;
            }
        }
        return true;
    }

    /**
     * @return bool
     */
    public static function rollback()
    {
        $rollback = true;
        foreach (self::$connections as $connection) {
            /** @var Driver $connection */
            if (!$connection->rollback()) {
                $rollback = false;
            }
        }
        return $rollback;
    }
}