<?php

namespace Simples\Core\Console;

/**
 * Class ModelService
 * @package Simples\Core\Console
 */
abstract class ModelService extends Service
{
    /**
     * @param $app
     */
    public static function execute($app)
    {
        $option = '';
        do {
            switch ($option) {
                case 'create': {
                    $commands = self::create();
                    echo json_encode($commands), PHP_EOL;
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
