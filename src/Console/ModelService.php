<?php

namespace Simples\Core\Console;

/**
 * Class ModelService
 * @package Simples\Core\Console
 */
abstract class ModelService extends GeneratorService
{
    /**
     * @var string
     */
    protected static $layer = 'model';

    /**
     * Ask for others layers
     * @param FileManager $fileManager
     */
    protected static function others(FileManager $fileManager)
    {
        if (in_array(read('Do you want to create Controller layer?', '[y/n]'), self::POSITIVES)) {
            $fileManager->execute('controller');
        }
        if (in_array(read('Do you want to create Repository layer?', '[y/n]'), self::POSITIVES)) {
            $fileManager->execute('repository');
        }
    }
}
