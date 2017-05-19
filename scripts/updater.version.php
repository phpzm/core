<?php

$phpzm = dirname(__DIR__, 2);
if (!isset($argv[1])) {
    exit(1);
}

$change = $argv[1];
if (!in_array($change, ['major', 'minor', 'patch'])) {
    echo "You need inform: ['major', 'minor', 'patch']";
    exit(1);
}

foreach (new DirectoryIterator($phpzm) as $file) {
    if ($file->isDot() or !file_exists($file->getPathname() . '/.dirty')) {
        continue;
    }
    $filename = $file->getPathname() . '/composer.json';
    $composer = json_decode(file_get_contents($filename));
    $peaces = explode('.', $composer->version);
    switch ($change) {
        case 'major':
            $peaces[0] = $peaces[0] + 1;
            $peaces[1] = 0;
            $peaces[2] = 0;
            break;
        case 'minor':
            $peaces[1] = $peaces[1] + 1;
            $peaces[2] = 0;
            break;
        case 'patch':
            $peaces[2] = $peaces[2] + 1;
            break;
    }
    $version = implode('.', $peaces);
    if ($version !== $composer->version) {
        touch($file->getPathname() . '/.dirty');

        $composer->version = $version;
        $json = json_encode($composer, JSON_UNESCAPED_SLASHES|JSON_PRETTY_PRINT);
        file_put_contents($filename, preg_replace('/^(  +?)\\1(?=[^ ])/m', '$1', $json));
    }
}
