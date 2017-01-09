<?php

error_reporting(E_ALL);

ini_set('display_errors', 'On');
ini_set('log_errors', 'On');
ini_set('track_errors', 'Off');
ini_set('html_errors', 'Off');

// native types
define('TYPE_BOOLEAN', 'boolean');
define('TYPE_INTEGER', 'integer');
define('TYPE_FLOAT', 'float');
define('TYPE_STRING', 'string');
define('TYPE_ARRAY', 'array');
define('TYPE_OBJECT', 'object');
define('TYPE_RESOURCE', 'resource');
define('TYPE_NULL', 'null');
define('TYPE_UNKNOWN_TYPE', 'unknown type');

// custom types
define('TYPE_DATE', 'date');

if (!function_exists('error_handler')) {
    /**
     * @param $code
     * @param $message
     * @param $file
     * @param $line
     * @throws ErrorException
     */
    function error_handler($code, $message, $file, $line)
    {
        throw new ErrorException($message, $code, 0, $file, $line);
    }
    set_error_handler("error_handler");
}