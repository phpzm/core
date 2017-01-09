<?php

if (!function_exists('env')) {
    /**
     * @return string
     */
    function env()
    {
        $filename = path(true, 'config', '.env');
        if (!file_exists($filename) || !is_file($filename)) {
            return '';
        }
        return trim(file_get_contents($filename));
    }
}

if (!function_exists('path')) {

    /**
     * @param $root
     * @return string
     */
    function path($root)
    {
        $args = func_get_args();
        $peaces = [];
        if (is_bool($root)) {
            array_shift($args);
            if ($root) {
                $peaces = [\Simples\Core\Kernel\App::$ROOT];
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

if (!function_exists('off')) {
    /**
     * @param $value
     * @param $property
     * @param $default
     *
     * @return mixed
     */
    function off($value, $property = null, $default = null)
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
        echo "<textarea style='width: 500px; height: 200px;'>{$contents}</textarea>";
        die();
    }
}


if (!function_exists('config')) {
    /**
     * @param $name
     * @return object
     */
    function config($name)
    {
        return \Simples\Core\Kernel\App::config($name);
    }
}

if (!function_exists('headerify')) {
    /**
     * @param $name
     * @return mixed
     */
    function headerify($name)
    {
        return str_replace(' ', '-', ucwords(strtolower(str_replace(['_','-'], ' ', $name))));
    }
}

if (!function_exists('str_replace_first')) {
    /**
     * @param $from
     * @param $to
     * @param $subject
     * @param $quote
     * @return mixed
     */
    function str_replace_first($from, $to, $subject, $quote = false)
    {
        if ($quote) {
            $from = '/'.preg_quote($from, '/').'/';
        }

        return preg_replace($from, $to, $subject, 1);
    }
}

if (!function_exists('fill_parameters')) {
    /**
     * @param $callback
     * @param $parameters
     * @return array
     */
    function fill_parameters($callback, $parameters)
    {
        $requiredParameters = 0;
        if (is_array($callback)) {
            if (isset($callback[0]) && $callback[1]) {
                $info = new \ReflectionMethod($callback[0], $callback[1]);
                $requiredParameters = $info->getNumberOfRequiredParameters();
            }
        } else {
            $info = new \ReflectionFunction($callback);
            $requiredParameters = $info->getNumberOfRequiredParameters();
        }
        if ($requiredParameters > count($parameters)) {
            for ($i = 0; $i < ($requiredParameters - count($parameters)); $i++) {
                $parameters[] = null;
            }
        }
        return $parameters;
    }
}

if (!function_exists('guid')) {
    /**
     * @param bool $brackets
     * @return string
     */
    function guid($brackets = false)
    {
        mt_srand((double)microtime() * 10000);

        $char = strtoupper(md5(uniqid(rand(), true)));
        $hyphen = chr(45);
        $uuid = substr($char, 0, 8) . $hyphen . substr($char, 8, 4) . $hyphen . substr($char, 12, 4) . $hyphen .
            substr($char, 16, 4) . $hyphen . substr($char, 20, 12);
        if ($brackets) {
            $uuid = chr(123) . $uuid . chr(125);
        }

        return $uuid;
    }
}
