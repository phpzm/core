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
    public static function escape($filename)
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
     * @param string $filename
     * @return string
     */
    public static function dir($filename)
    {
        return dirname($filename);
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

    /**
     * @param $filename
     * @return bool
     */
    public static function touch($filename)
    {
        return touch($filename);
    }

    /**
     * @param $filename
     * @param string $mode
     * mode    Description
     * 'r'    Open for reading only; place the file pointer at the beginning of the file.
     * 'r+'    Open for reading and writing; place the file pointer at the beginning of the file.
     * 'w'    Open for writing only; place the file pointer at the beginning of the file and truncate the file to zero length. If the file does not exist, attempt to create it.
     * 'w+'    Open for reading and writing; place the file pointer at the beginning of the file and truncate the file to zero length. If the file does not exist, attempt to create it.
     * 'a'    Open for writing only; place the file pointer at the end of the file. If the file does not exist, attempt to create it. In this mode, fseek() has no effect, writes are always appended.
     * 'a+'    Open for reading and writing; place the file pointer at the end of the file. If the file does not exist, attempt to create it. In this mode, fseek() only affects the reading position, writes are always appended.
     * 'x'    Create and open for writing only; place the file pointer at the beginning of the file. If the file already exists, the fopen() call will fail by returning FALSE and generating an error of level E_WARNING. If the file does not exist, attempt to create it. This is equivalent to specifying O_EXCL|O_CREAT flags for the underlying open(2) system call.
     * 'x+'    Create and open for reading and writing; otherwise it has the same behavior as 'x'.
     * 'c'    Open the file for writing only. If the file does not exist, it is created. If it exists, it is neither truncated (as opposed to 'w'), nor the call to this function fails (as is the case with 'x'). The file pointer is positioned on the beginning of the file. This may be useful if it's desired to get an advisory lock (see flock()) before attempting to modify the file, as using 'w' could truncate the file before the lock was obtained (if truncation is desired, ftruncate() can be used after the lock is requested).
     * 'c+'    Open the file for reading and writing; otherwise it has the same behavior as 'c'.
     *
     * @return resource
     */
    public static function open($filename, $mode = 'r')
    {
        return fopen($filename, $mode);
    }

    /**
     * @param $filename
     * @return bool
     */
    public static function close($filename)
    {
        return fclose($filename);
    }

    /**
     * @param $handle
     * @return string
     */
    public static function gets($handle)
    {
        return fgets($handle);
    }

}