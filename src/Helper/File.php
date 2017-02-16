<?php

namespace Simples\Core\Helper;

/**
 * Class File
 * @package Simples\Core\Helper
 */
abstract class File
{
    /**
     * @param $filename
     * @return bool
     */
    public static function exists($filename)
    {
        return file_exists($filename) && !is_dir($filename);
    }

    /**
     * @param $filename
     * @return bool
     */
    public static function isWritable($filename)
    {
        return is_writable($filename);
    }

    /**
     * @param $filename
     * @param $content
     * @return int|null
     */
    public static function write($filename, $content)
    {
        if (Directory::make(dirname($filename))) {
            return file_put_contents($filename, $content);
        }
        return null;
    }

    /**
     * @param $filename
     * @return null|string
     */
    public static function read($filename)
    {
        if (File::exists($filename)) {
            return file_get_contents($filename);
        }
        return null;
    }

    /**
     * @param $filename
     * @return mixed
     */
    protected static function escape($filename)
    {
        return preg_replace('/[^A-Za-z0-9_\-]/', '_', $filename);
    }

    /**
     * @param string $filename
     * @param string $suffix
     * @return string
     */
    public static function name($filename, $suffix = null)
    {
        return basename($filename, $suffix);
    }

    /**
     * @param $filename
     * @return string
     */
    public static function extension($filename)
    {
        return pathinfo($filename, PATHINFO_EXTENSION);
    }

    /**
     * @param $filename
     * @return string
     */
    public static function mimeType($filename)
    {
        if (self::exists($filename)) {
            if (function_exists('mime_content_type')) {
                return mime_content_type($filename);
            }
        }
        return 'unknown';
    }

    /**
     * @param $filename
     * @return bool|null
     */
    public static function destroy($filename)
    {
        if (self::exists($filename)) {
            return unlink($filename);
        }
        return null;
    }

    /**
     * @param $source
     * @param $target
     * @return bool|null
     */
    public static function copy($source, $target)
    {
        if (self::exists($source)) {
            if (self::write($target, 'x')) {
                unlink($target);
                return copy($source, $target);
            }
        }
        return null;
    }

    /**
     * @param $source
     * @param $target
     * @return bool|null
     */
    public static function move($source, $target)
    {
        if (self::copy($source, $target)) {
            return self::destroy($source);
        }
        return null;
    }
}
