<?php
/*
 -------------------------------------------------------------------
 | @project: api
 | @package: Simples\Core\Kernel
 | @file: Lang.php
 -------------------------------------------------------------------
 | @user: william 
 | @creation: 02/01/17 15:36
 | @copyright: fagoc.br / gennesis.io / arraysoftware.net
 | @license: MIT
 -------------------------------------------------------------------
 | @description:
 | PHP class
 |
 */

namespace Simples\Core\Kernel;

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
     * @param $i18
     * @param array $parameters
     * @return string
     */
    public static function lang($scope, $i18, array $parameters = [])
    {
        $string = "Lang '{$scope}.{$i18}' not found";

        $languages = App::options('lang');

        $filename = path(true, "app/resources/locales/{$languages['default']}/{$scope}.php");
        if (!file_exists($filename)) {
            $filename = path(true, "app/resources/locales/{$languages['fallback']}/{$scope}.php");
        }

        if (file_exists($filename)) {

            /** @noinspection PhpIncludeInspection */
            $phrases = include $filename;
            if (isset($phrases[$i18])) {
                $string = $phrases[$i18];
                foreach ($parameters as $key => $value) {
                    $string = str_replace('{' . $key . '}', out($value, false), $string);
                }
            }
        }

        return $string;
    }
}