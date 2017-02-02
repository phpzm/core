<?php

namespace Simples\Core\Console;

/**
 * Class ModelService
 * @package Simples\Core\Console
 */
abstract class ModelService extends GeneratorService
{
    /**
     * Ask for others layers
     * @param FileManager $fileManager
     */
    protected static function others(FileManager $fileManager)
    {
        self::ask('Do you want to create Controller layer?', '[y/n]');
        if (in_array(self::read(), self::POSITIVES)) {
            $fileManager->execute('controller');
        }
        self::ask('Do you want to create Repository layer?', '[y/n]');
        if (in_array(self::read(), self::POSITIVES)) {
            $fileManager->execute('repository');
        }
    }
}
