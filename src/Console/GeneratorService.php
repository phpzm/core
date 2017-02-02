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
     */
    public static function execute(App $app)
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

            echo "[ model ]$ ";
            $option = trim(fgets(STDIN));
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
            echo "[ model.create ]{$message}";
            $option = trim(fgets(STDIN));
        } while (!in_array($option, Service::KILLERS));

        return null;
    }

    /**
     * Ask a question to user
     * @param string $question
     * @param string $options
     */
    protected static function ask(string $question, string $options = '[y/n]')
    {
        print "{$question} ";
        if ($options) {
            print "{$options} ";
        }
        print "$ ";
    }

    /**
     * @return string
     */
    protected static function read()
    {
        return trim(fgets(STDIN));
    }

    /**
     * Ask for others layers
     * @param FileManager $fileManager
     * @throws \Exception
     */
    protected abstract static function others(FileManager $fileManager);

}