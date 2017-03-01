<?php

namespace Simples\Core\Console;

use Simples\Core\Kernel\App;

/**
 * Class GeneratorService
 * @package Simples\Core\Console
 */
abstract class GeneratorService extends Service
{
    /**
     * @var array
     */
    const POSITIVES = ['y', 'yes', 'yep'];

    /**
     * @var string
     */
    protected static $layer = '';

    /**
     * @param App $app
     * @param array $parameters
     * @SuppressWarnings("unused")
     */
    public static function execute(App $app, array $parameters = [])
    {
        $option = '';
        do {
            switch ($option) {
                case 'create': {
                    $commands = self::create($app);

                    $replacements = [
                        'namespace' => [
                            'field' => '${NAMESPACE}',
                            'value' => $app::config('app.namespace') . $commands['namespace']
                        ],
                        'name' => [
                            'field' => '${NAME}',
                            'value' => $commands['name']
                        ]
                    ];
                    $fileManager = new FileManager($commands['namespace'], $commands['name'], $replacements);

                    $fileManager->execute(static::$layer);

                    static::others($fileManager);
                    break;
                }
            }
            echo " # MODEL\n";
            echo " Choose one option:\n";
            echo "    - create\n";
            // echo "    - refactor\n";
            // echo "    - remove\n";

            $option = read('[ model ]$ ');
        } while (!in_array($option, Service::KILLERS));
    }

    /**
     * @param App $app
     * @return array|null
     */
    protected static function create(App $app)
    {
        $control = 'action';
        $option = '';
        $message = '';
        $commands = [];
        do {
            switch ($control) {
                case 'action': {
                    $commands['action'] = $option;
                    $message = ' namespace: $ [' . $app::config('app.namespace') . ']';
                    $control = 'namespace';
                    break;
                }
                case 'namespace': {
                    $commands['namespace'] = $option;
                    $message = ' name: $ ';
                    $control = 'name';
                    break;
                }
                case 'name': {
                    $commands['name'] = $option;
                    return $commands;
                    break;
                }
            }
            $option = read("[ model.create ]{$message}");
        } while (!in_array($option, Service::KILLERS));

        return null;
    }

    /**
     * Ask for others layers
     * @param FileManager $fileManager
     * @throws \Exception
     */
    abstract protected static function others(FileManager $fileManager);
}
