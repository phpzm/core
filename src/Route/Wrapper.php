<?php

namespace Simples\Core\Route;

use Simples\Core\Kernel\App;

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
        self::$messages[] = [
            'type' => $type,
            'message' => $message,
            'trace' => self::trace()
        ];
    }

    /**
     * @return array
     */
    public static function messages()
    {
        return self::$messages;
    }

    /**
     * @return array
     */
    protected static function trace()
    {
        $stack = App::beautifulTrace(debug_backtrace());

        return array_slice($stack, 3);
    }
}
