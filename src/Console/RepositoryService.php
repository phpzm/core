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
        if (in_array(read('Do you want to create Model layer?', '[y/n]'), self::POSITIVES)) {
            $fileManager->execute('model');
        }
        if (in_array(read('Do you want to create Controller layer?', '[y/n]'), self::POSITIVES)) {
            $fileManager->execute('controller');
        }
    }
}
