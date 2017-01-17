<?php

namespace Simples\Core\Helper;

use FilesystemIterator;

/**
 * Class Directory
 * @package Simples\Core\Helper
 */
abstract class Directory
{
    /**
     * @param $dir
     * @return int
     */
    public static function count($dir)
    {
        return iterator_count(new FilesystemIterator($dir, FilesystemIterator::SKIP_DOTS));
    }

    /**
     * @param $dir
     * @return bool
     */
    public static function exists($dir)
    {
        return is_dir($dir);
    }

    /**
     * @param $dir
     * @return bool
     */
    public static function make($dir)
    {
        $make = is_dir($dir);
        if (!$make) {
            $make = mkdir($dir, 0755, true);
        }
        return $make;
    }

    /**
     * @param $dir
     * @return bool
     */
    public static function remove($dir)
    {
        $files = array_diff(scandir($dir), ['.', '..']);
        foreach ($files as $file) {
            (is_dir("$dir/$file")) ? self::remove("$dir/$file") : unlink("$dir/$file");
        }
        return rmdir($dir);
    }

    /**
     * @param $source
     * @param $target
     * @return bool
     */
    public static function rename($source, $target)
    {
        return rename($source, $target);
    }

    /**
     * @param $dir
     * @return array
     */
    public static function getFiles($dir)
    {
        $files = [];

        if (self::exists($dir)) {
            foreach (new \DirectoryIterator($dir) as $fileInfo) {
                if ($fileInfo->isDot()) {
                    continue;
                }
                $files[] = $fileInfo->getRealPath();
            }
        }

        return $files;
    }
}
