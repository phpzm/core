<?php

error_reporting(E_ALL);

// system settings
ini_set('display_errors', env('ERRORS_DISPLAY', 'On'));
ini_set('log_errors', env('ERRORS_DISPLAY', 'On'));
ini_set('track_errors', env('ERRORS_DISPLAY', 'Off'));
ini_set('html_errors', env('ERRORS_DISPLAY', 'Off'));

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

// used to compose path do generator
define('TEMPLATE_DIR', 'resources/templates');

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
        throw new ErrorException($message, $code, 1, $file, $line);
    }
    set_error_handler("error_handler");
}
