<?php

$phpzm = dirname(__DIR__, 2);

var_dump($argv);

foreach (new DirectoryIterator($phpzm) as $fileInfo) {
    if ($fileInfo->isDot()) {
        continue;
    }
    echo $fileInfo->getFilename(), PHP_EOL;
}
