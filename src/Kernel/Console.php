<?php

namespace Simples\Core\Kernel;

/**
 * Class Console
 * @package Simples\Core\Kernel
 */
class Console
{
    /**
     * @param $argv
     * @return array
     */
    public function parseParameters($argv)
    {
        array_shift($argv);

        $parameters = [];
        foreach ($argv as $arg) {
            $in = explode("=", $arg);
            if (count($in) == 2) {
                $parameters[$in[0]] = $in[1];
            } else {
                $parameters[$in[0]] = 0;
            }
        }
        return $parameters;
    }
}
