<?php

namespace Simples\Core\Helper;

use Error;

/**
 * Class Json
 * @package Simples\Core\Helper
 */
abstract class Json
{
    /**
     * @param $mixed
     * @param int $options
     * @param int $depth
     * @return string
     * @throws Error
     */
    public static function encode($mixed, $options = 0, $depth = 512)
    {
        $string = json_encode($mixed, $options, $depth);

        switch (json_last_error()) {
            case JSON_ERROR_NONE:
                $error = '';
                break;
            case JSON_ERROR_DEPTH:
                $error = 'Maximum stack depth exceeded';
                break;
            case JSON_ERROR_STATE_MISMATCH:
                $error = 'Underflow or the modes mismatch';
                break;
            case JSON_ERROR_CTRL_CHAR:
                $error = 'Unexpected control character found';
                break;
            case JSON_ERROR_SYNTAX:
                $error = 'Syntax error, malformed JSON';
                break;
            case JSON_ERROR_UTF8:
                $error = 'Malformed UTF-8 characters, possibly incorrectly encoded';
                break;
            default:
                $error = 'Unknown error';
                break;
        }

        if ($error) {
            throw new Error('Json::encode error: ' . $error);
        }

        return $string;
    }

    /**
     * @param $string
     * @param bool $assoc
     * @param int $depth
     * @param int $options
     * @return mixed
     * @throws Error
     */
    public static function decode($string, $assoc = false, $depth = 512, $options = 0)
    {
        $mixed = json_decode($string, $assoc, $depth, $options);

        switch (json_last_error()) {
            case JSON_ERROR_NONE:
                $error = '';
                break;
            case JSON_ERROR_DEPTH:
                $error = 'Maximum stack depth exceeded';
                break;
            case JSON_ERROR_STATE_MISMATCH:
                $error = 'Underflow or the modes mismatch';
                break;
            case JSON_ERROR_CTRL_CHAR:
                $error = 'Unexpected control character found';
                break;
            case JSON_ERROR_SYNTAX:
                $error = 'Syntax error, malformed JSON';
                break;
            case JSON_ERROR_UTF8:
                $error = 'Malformed UTF-8 characters, possibly incorrectly encoded';
                break;
            default:
                $error = 'Unknown error';
                break;
        }

        if ($error) {
            throw new Error('Json::decode error: ' . $error);
        }

        return $mixed;
    }

    /**
     * @param $string
     * @return bool
     */
    public static function isJson($string)
    {
        json_decode($string);
        return (json_last_error() == JSON_ERROR_NONE);
    }
}
