<?php

namespace Simples\Core\Unit;

/**
 * Class Origin
 * @package Simples\Core\Unit
 */
class Origin
{
    /**
     * @return string
     */
    function __toString()
    {
        $properties = [];
        foreach ($this as $key => $value) {
            $properties = [$key => $value];
        }
        return json_encode($properties);
    }

}