<?php

namespace Simples\Core\Flow;

/**
 * Class Wrapper
 * @package Simples\Core\Flow
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
    public static function error($message)
    {
        self::message('error', $message);
    }

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
     * @param $type
     * @param $message
     */
    public static function message($type, $message)
    {
        self::$messages[] = ['status' => $type, 'message' => $message];
    }

    /**
     * @return array
     */
    public static function messages()
    {
        return self::$messages;
    }

    public static function log($message, $values)
    {
        self::info([$message, $values]);
    }

}