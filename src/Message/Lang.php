<?php

namespace Simples\Core\Message;

use Simples\Core\Helper\File;
use Simples\Core\Kernel\App;

/**
 * @method static string validation($i18, array $parameters = [])
 * @method static string auth($i18, array $parameters = [])
 *
 * Class Lang
 * @package Simples\Core\Kernel
 */
abstract class Lang
{
    /**
     * @param $default
     * @param string $fallback
     */
    public static function locale($default, $fallback = '')
    {
        App::options('lang', ['default' => $default, 'fallback' => $fallback]);
    }

    /**
     * @param $name
     * @param $arguments
     * @return string
     */
    public static function __callStatic($name, $arguments)
    {
        if (isset($arguments[1])) {
            return self::lang($name, $arguments[0], $arguments[1]);
        }
        return self::lang($name, $arguments[0]);
    }


    /**
     * @param $scope
     * @param $path
     * @param array $parameters
     * @return string
     */
    public static function lang($scope, $path, array $parameters = [])
    {
        $i18n = "Lang '{$scope}.{$path}' not found";

        $languages = App::options('lang');

        $filename = path(true, "app/resources/locales/{$languages['default']}/{$scope}.php");
        if (!File::exists($filename)) {
            $filename = path(true, "app/resources/locales/{$languages['fallback']}/{$scope}.php");
        }

        if (File::exists($filename)) {

            /** @noinspection PhpIncludeInspection */
            $phrases = include $filename;

            $i18n = search($phrases, $path);
            if (gettype($i18n) === TYPE_STRING) {
                return self::replace($i18n, $parameters);
            }
        }
        return $i18n;
    }

    /**
     * @param $i18n
     * @param $parameters
     * @return mixed
     */
    public static function replace($i18n, $parameters)
    {
        foreach ($parameters as $key => $value) {
            $i18n = str_replace('{' . $key . '}', out($value, false), $i18n);
        }
        return $i18n;
    }

}