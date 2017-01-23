<?php

namespace Simples\Core\Helper;

/**
 * Class Text
 * @package Simples\Core\Helper
 */
abstract class Text
{
    /**
     * @param $string
     * @param $search
     * @param $replace
     * @param null $count
     * @return mixed
     */
    public static function replace($string, $search, $replace, &$count = null)
    {
        if ($count) {
            str_replace($search, $replace, $string, $count);
        }
        return str_replace($search, $replace, $string);
    }

    /**
     * @param $input
     * @param $pad_length
     * @param null $pad_string
     * @param null $pad_type
     * @return string
     */
    public static function pad($input, $pad_length, $pad_string = null, $pad_type = null)
    {
        return str_pad($input, $pad_length, $pad_string, $pad_type);
    }
}
