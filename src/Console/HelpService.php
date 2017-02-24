<?php

namespace Simples\Core\Console;

use Simples\Core\Kernel\App;

/**
 * Class HelpService
 * @package Simples\Core\Console
 */
abstract class HelpService extends Service
{
    /**
     * @param App $app
     * @SuppressWarnings("unused")
     */
    public static function execute(App $app)
    {
        echo " # HELP\n";
        echo " Choose one option:\n";
        echo "    - route: show the routes\n";
        echo "    - model: service to manage models\n";
        echo "    - controller: service to manage controllers\n";
        echo "    - repository: service to manage repositories\n";
        echo "    - migration: create and run revisions\n";
        echo "\n";
        echo "    - exit: finish application\n";
    }
}
