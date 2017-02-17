<?php

namespace Simples\Core\Kernel;

/**
 * Class Console
 * @package Simples\Core\Kernel
 */
class Console
{
    /**
     * @param array $argv
     * @return array
     */
    public function parseParameters(array $argv): array
    {
        array_shift($argv);

        $parameters = [];
        foreach ($argv as $arg) {
            $in = explode("=", $arg);
            $value = 0;
            if (count($in) == 2) {
                $value = $in[1];
            }
            $parameters[$in[0]] = $value;
        }
        return $parameters;
    }
}
