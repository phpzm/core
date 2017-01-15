<?php

namespace Simples\Core\Route;

/**
 * Class Wrapper
 * @package Simples\Core\Route
 */
abstract class Wrapper
{
    /**
     * @var array
     */
    private static $messages = [];

    /**
     * @param $message
     */
    public static function warning($message)
    {
        self::message('warning', $message);
    }

    /**
     * @param $message
     */
    public static function info($message)
    {
        self::message('info', $message);
    }

    /**
     * @param $message
     */
    public static function buffer($message)
    {
        self::message('buffer', $message);
    }

    /**
     * @param $log
     */
    public static function log($log)
    {
        self::message('log', $log);
    }

    /**
     * @param $type
     * @param $message
     */
    public static function message($type, $message)
    {
        self::$messages[] = ['type' => $type, 'message' => $message];
    }

    /**
     * @return array
     */
    public static function messages()
    {
        return self::$messages;
    }

}