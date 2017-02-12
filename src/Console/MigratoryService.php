<?php

namespace Simples\Core\Console;

use Simples\Core\Kernel\App;
use ReflectionClass;
use ReflectionMethod;

/**
 * Class MigratoryService
 * @package Simples\Core\Console
 */
class MigratoryService extends Service
{
    /**
     * @param App $app
     */
    public static function execute(App $app)
    {
        // TODO: Implement execute() method.
    }

    /**
     * @param string $class
     * @param string $method
     * @param string $line
     * @return bool
     */
    public static function addStatement(string $class, string $method, string $line): bool
    {
        $classReflection = new ReflectionClass($class);
        $fileName = $classReflection->getFileName();

        if (!empty($fileName)) {

            $classStartLine = 0;
            $classEndLine = $classReflection->getEndLine();
            $classLines = $classEndLine - $classStartLine;

            $classContent = array_slice(file($fileName), 0, $classLines);

            $methodReflection = new ReflectionMethod(App::class, $method);
            $methodEndLine = $methodReflection->getEndLine();

            $new = '        ' . $line . PHP_EOL;
            array_splice($classContent, $methodEndLine - $classStartLine - 1, 0, $new);

            return !!file_put_contents($fileName, implode("", $classContent));
        }
        return false;
    }
}