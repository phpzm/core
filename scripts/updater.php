<?php

$phpzm = dirname(__DIR__, 2);

foreach (new DirectoryIterator($phpzm) as $fileInfo) {
    if ($fileInfo->isDot()) {
        continue;
    }
    echo $fileInfo->getFilename(), PHP_EOL;
}
