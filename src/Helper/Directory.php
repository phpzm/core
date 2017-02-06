<?php

namespace Simples\Core\Helper;

use FilesystemIterator;
use DirectoryIterator;

/**
 * Class Directory
 * @package Simples\Core\Helper
 */
abstract class Directory
{
    /**
     * @param string $dir
     * @return int
     */
    public static function count(string $dir): int
    {
        return (int)iterator_count(new FilesystemIterator($dir, FilesystemIterator::SKIP_DOTS));
    }

    /**
     * @param string $dir
     * @return bool
     */
    public static function exists(string $dir): bool
    {
        return is_dir($dir);
    }

    /**
     * @param string $dir
     * @return bool
     */
    public static function make(string $dir): bool
    {
        $make = is_dir($dir);
        if (!$make) {
            $make = mkdir($dir, 0755, true);
        }
        return $make;
    }

    /**
     * @param string $dir
     * @return bool
     */
    public static function remove(string $dir): bool
    {
        $files = array_diff(scandir($dir), ['.', '..']);
        foreach ($files as $file) {
            (is_dir("$dir/$file")) ? self::remove("$dir/$file") : unlink("$dir/$file");
        }
        return rmdir($dir);
    }

    /**
     * @param string $source
     * @param string $target
     * @return bool
     */
    public static function rename(string $source, string $target): bool
    {
        return rename($source, $target);
    }

    /**
     * @param string $dir
     * @return array
     */
    public static function getFiles(string $dir): array
    {
        $files = [];

        if (self::exists($dir)) {
            foreach (new DirectoryIterator($dir) as $fileInfo) {
                if ($fileInfo->isDot()) {
                    continue;
                }
                $files[] = $fileInfo->getRealPath();
            }
        }

        return $files;
    }
}
