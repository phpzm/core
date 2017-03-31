<?php

$phpzm = dirname(__DIR__, 2);
if (!isset($argv[1])) {
    exit(1);
}

$version = $argv[1];
foreach (new DirectoryIterator($phpzm) as $file) {
    if ($file->isDot() or $file->getFilename() === 'core') {
        continue;
    }
    $filename = $file->getPathname() . '/composer.json';
    $composer = json_decode(file_get_contents($filename));
    $peaces = explode('.', $composer->version);
    switch ($version) {
        case '1':
            $peaces[0] = $peaces[0] + 1;
            $peaces[1] = 0;
            $peaces[2] = 0;
            break;
        case '2':
            $peaces[1] = $peaces[1] + 1;
            $peaces[2] = 0;
            break;
        case '3':
            $peaces[2] = $peaces[2] + 1;
            break;
    }
    $release = implode('.', $peaces);
    $composer->version = $release;
    $json = json_encode($composer, JSON_UNESCAPED_SLASHES|JSON_PRETTY_PRINT);
    file_put_contents($filename, preg_replace('/^(  +?)\\1(?=[^ ])/m', '$1', $json));

    //echo $file->getFilename(), ' => ', $composer->version, PHP_EOL;
}
