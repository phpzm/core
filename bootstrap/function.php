<?php


if (!function_exists('path')) {

    /**
     * @uses \Simples\Core\App::$ROOT
     * @uses DIRECTORY_SEPARATOR
     * @uses func_get_args()
     *
     * @param $root
     *
     * @return string
     */
    function path($root)
    {
        $args = func_get_args();
        $peaces = [];
        if (is_bool($root)) {
            array_shift($args);
            if ($root) {
                $peaces = [\Simples\Core\App::$ROOT];
            }
        }
        $path = array_merge($peaces, $args);

        return implode(DIRECTORY_SEPARATOR, $path);
    }
}

if (!function_exists('out')) {
    /**
     *
     * @param $value
     * @param bool $print
     * @param string $type
     * @return string
     */
    function out($value, $print = true, $type = null)
    {
        $type = iif($type, gettype($value));
        switch ($type) {
            case TYPE_BOOLEAN:
                $value = $value ? 'true' : 'false';
                break;
            case TYPE_INTEGER:
            case TYPE_FLOAT:
            case TYPE_STRING:
                $value = trim($value);
                break;
            case TYPE_ARRAY:
            case TYPE_OBJECT:
                $value = json_encode($value);
                break;
            case TYPE_RESOURCE:
                $value = serialize($value);
                break;
            case TYPE_NULL:
            case TYPE_UNKNOWN_TYPE:
                $value = '';
        }
        if ($print) {
            print $value;
        }
        return $value;
    }
}

if (!function_exists('iif')) {
    /**
     * @param $value
     * @param $default
     * @return mixed
     */
    function iif($value, $default = false)
    {
        return is_null($value) ? $default : $value;
    }
}

if (!function_exists('sif')) {
    /**
     * @param $value
     * @param $property
     * @param $default
     *
     * @return mixed
     */
    function sif($value, $property = null, $default = null)
    {
        if (is_null($property)) {
            return $default;
        }

        if (is_array($value)) {
            return isset($value[$property]) ? $value[$property] : $default;
        } else if (is_object($value)) {
            return isset($value->$property) ? $value->$property : $default;
        }
        return $default;
    }
}

if (!function_exists('stop')) {
    /**
     *
     */
    function stop()
    {
        ob_start();
        var_dump(func_get_args());
        $contents = ob_get_contents();
        ob_end_clean();
        echo "<textarea>{$contents}</textarea>";
        die("");
    }
}