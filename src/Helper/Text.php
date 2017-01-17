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
}
