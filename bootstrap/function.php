<?php

if (!function_exists('env')) {
    /**
     * @param string $property
     * @param mixed $default (null)
     * @return string
     */
    function env($property, $default = null)
    {
        $filename = path(true, '.env');
        if (file_exists($filename) && is_file($filename)) {
            $properties = parse_ini_file($filename);
            if (is_array($properties)) {
                return off($properties, $property);
            }
        }
        return $default;
    }
}

if (!function_exists('path')) {

    /**
     * @param string $root
     * @return string
     */
    function path($root)
    {
        $args = func_get_args();
        $peaces = [];
        if (is_bool($root)) {
            array_shift($args);
            if ($root) {
                $dir = \Simples\Core\Kernel\App::options('root');
                if (!$dir) {
                    $dir = dirname(__DIR__, 4);
                }
                $peaces = [$dir];
            }
        }
        $path = array_merge($peaces, $args);

        return implode(DIRECTORY_SEPARATOR, $path);
    }
}

if (!function_exists('storage')) {
    /**
     * @param string $path
     * @return string
     */
    function storage($path)
    {
        return path(true, 'storage', $path);
    }
}

if (!function_exists('resources')) {
    /**
     * @param string $path
     * @return string
     */
    function resources($path)
    {
        return path(true, 'app/resources', $path);
    }
}

if (!function_exists('out')) {
    /**
     *
     * @param mixed $value
     * @param bool $print (true)
     * @param string $type (null)
     * @return string
     */
    function out($value, $print = true, $type = null)
    {
        $type = of($type, gettype($value));
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

if (!function_exists('of')) {
    /**
     * @param mixed $value
     * @param mixed $default (false)
     * @return mixed
     */
    function of($value, $default = false)
    {
        return is_null($value) ? $default : $value;
    }
}

if (!function_exists('off')) {
    /**
     * @param mixed $value
     * @param mixed $property (null)
     * @param mixed $default (null)
     *
     * @return mixed
     */
    function off($value, $property = null, $default = null)
    {
        if (is_null($property)) {
            return $default;
        }
        if (!$value) {
            return $default;
        }

        if (is_array($value)) {
            return isset($value[$property]) ? $value[$property] : $default;
        } elseif (is_object($value)) {
            return isset($value->$property) ? $value->$property : $default;
        }
        return $default;
    }
}

if (!function_exists('stop')) {
    /**
     * @die
     */
    function stop()
    {
        ob_start();
        echo json_encode(func_get_args());
        $contents = ob_get_contents();
        ob_end_clean();
        out($contents);
        die;
    }
}

if (!function_exists('config')) {
    /**
     * @param string $name
     * @return mixed
     */
    function config($name)
    {
        return \Simples\Core\Kernel\App::config($name);
    }
}

if (!function_exists('headerify')) {
    /**
     * @param string $name
     * @return string
     */
    function headerify($name)
    {
        return str_replace(' ', '-', ucwords(strtolower(str_replace(['_', '-'], ' ', $name))));
    }
}

if (!function_exists('str_replace_first')) {
    /**
     * @param string $from
     * @param string $to
     * @param string $subject
     * @param bool $quote (false)
     * @return mixed
     */
    function str_replace_first($from, $to, $subject, $quote = false)
    {
        if ($quote) {
            $from = '/' . preg_quote($from, '/') . '/';
        }

        return preg_replace($from, $to, $subject, 1);
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

if (!function_exists('is_iterator')) {
    /**
     * @param mixed $var
     * @return bool
     */
    function is_iterator($var)
    {
        return (is_array($var) || $var instanceof Traversable);
    }
}

if (!function_exists('throw_format')) {
    /**
     * @param Throwable $throw
     * @return string
     */
    function throw_format(Throwable $throw)
    {
        return "[{$throw->getMessage()}] ON [{$throw->getFile()}] AT [{$throw->getLine()}]";
    }
}

if (!function_exists('search')) {
    /**
     * @param mixed $context
     * @param array|string $path
     * @return mixed|null
     */
    function search($context, $path)
    {
        if (!is_array($path)) {
            $path = explode('.', $path);
        }
        foreach ($path as $piece) {
            if (!is_array($context) || !array_key_exists($piece, $context)) {
                return null;
            }
            $context = $context[$piece];
        }
        return $context;
    }
}

if (!function_exists('read')) {
    /**
     * @param string $prompt
     * @param string $options
     * @return string
     */
    function read(string $prompt = '$ ', string $options = ''): string
    {
        if ($options) {
            $prompt = "{$prompt} {$options}\$ ";
        }
        if (PHP_OS === 'WINNT') {
            echo $prompt;
            $line = stream_get_line(STDIN, 1024, PHP_EOL);
        } else {
            $line = readline("{$prompt}");
        }
        readline_add_history($line);

        return trim($line);
    }
}

if (!function_exists('clearpath')) {
    /**
     * @param string $path
     * @return string
     */
    function clearpath(string $path): string
    {
        return implode('/', array_filter(explode('/', $path), function ($value) {
            if (!in_array($value, ['..', '.'])) {
                return $value;
            }
            return null;
        }));
    }
}
