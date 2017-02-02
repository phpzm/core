<?php

namespace Simples\Core\Console;

use Simples\Core\Kernel\App;

/**
 * Class ModelService
 * @package Simples\Core\Console
 */
abstract class ModelService extends Service
{
    /**
     * @param App $app
     */
    public static function execute(App $app)
    {
        $option = '';
        do {
            switch ($option) {
                case 'create': {
                    $commands = self::create();

                    $replacements = [
                        'namespace' =>
                            [
                                'field' => '${NAMESPACE}',
                                'value' => $commands['namespace']
                            ],
                        'name' =>
                            [
                                'field' => '${NAME}',
                                'value' => $commands['name']
                            ]
                    ];

                    $fileManager = new FileManager($commands['name'],$replacements);


                    $fileManager->execute('model');

                    print "Do you want to create Controller layer?";
                    $option = trim(fgets(STDIN));
                    if(!in_array($option, ['no', 'nao', 'nop', 'n'])){
                        $fileManager->execute('controller');
                    }
                    print "Do you want to create Repository layer?";
                    $option = trim(fgets(STDIN));
                    if(!in_array($option, ['no', 'nao', 'nop', 'n'])){
                        $fileManager->execute('repository');
                    }

                    break;
                }
            }
            echo " # MODEL\n";
            echo " Choose one option:\n";
            echo "    - create\n";
            echo "    - refactor\n";
            echo "    - remove\n";

            echo "[ model ]$ ";
            $option = trim(fgets(STDIN));
        } while (!in_array($option, Service::KILLERS));
    }

    /**
     * @return array|null
     */
    private static function create()
    {
        $control = 'action';
        $option = '';
        $message = '';
        $commands = [];
        do {
            switch ($control) {
                case 'action': {
                    $commands['action'] = $option;
                    $message = ' namespace:';
                    $control = 'namespace';
                    break;
                }
                case 'namespace': {
                    $commands['namespace'] = $option;
                    $message = ' name:';
                    $control = 'name';
                    break;
                }
                case 'name': {
                    $commands['name'] = $option;
                    return $commands;
                    break;
                }
            }
            echo "[ model.create ]{$message}$ ";
            $option = trim(fgets(STDIN));
        } while (!in_array($option, Service::KILLERS));

        return null;
    }
}
