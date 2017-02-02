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
        self::ask('Do you want to create Model layer?', '[y/n]');
        if (in_array(self::read(), self::POSITIVES)) {
            $fileManager->execute('model');
        }
        self::ask('Do you want to create Repository layer?', '[y/n]');
        if (in_array(self::read(), self::POSITIVES)) {
            $fileManager->execute('repository');
        }
    }
}
