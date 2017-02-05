<?php

namespace Simples\Core\Console;

/**
 * Class ControllerService
 * @package Simples\Core\Console
 */
abstract class ControllerService extends GeneratorService
{
    /**
     * @var string
     */
    protected static $layer = 'controller';

    /**
     * Ask for others layers
     * @param FileManager $fileManager
     */
    protected static function others(FileManager $fileManager)
    {
        if (in_array(read('Do you want to create Model layer?', '[y/n]'), self::POSITIVES)) {
            $fileManager->execute('model');
        }
        if (in_array(read('Do you want to create Repository layer?', '[y/n]'), self::POSITIVES)) {
            $fileManager->execute('repository');
        }
    }
}
