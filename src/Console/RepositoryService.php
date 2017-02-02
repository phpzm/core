<?php

namespace Simples\Core\Console;

/**
 * Class RepositoryService
 * @package Simples\Core\Console
 */
abstract class RepositoryService extends GeneratorService
{
    /**
     * @var string
     */
    protected static $layer = 'repository';

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
        self::ask('Do you want to create Controller layer?', '[y/n]');
        if (in_array(self::read(), self::POSITIVES)) {
            $fileManager->execute('controller');
        }
    }
}
