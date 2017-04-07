<?php

$phpzm = dirname(__DIR__, 2);

$versions = [];
$packages = [];
foreach (new DirectoryIterator($phpzm) as $file) {
    if ($file->isDot()) {
        continue;
    }
    $filename = $file->getPathname() . '/composer.json';
    $composer = json_decode(file_get_contents($filename));

    $package = 'phpzm/' . $file->getFilename();

    $versions[$package] = $composer->version;
    $packages[$package] = [
        'filename' => $filename,
        'composer' => $composer
    ];
}

foreach ($packages as $package => $project) {
    echo $package, PHP_EOL;
    $requires = (array)$project['composer']->require;
    foreach ($requires as $key => $value) {
        if (isset($versions[$key])) {
            echo '    ', $key , ' [', substr($value, 2), ' => ', $versions[$key], '] ', PHP_EOL;
            /** @noinspection PhpVariableVariableInspection */
            $requires[$key] = '>=' . $versions[$key];
        }
    }
    if ($project['composer']->require !== $requires) {
        touch(file_exists($file->getPathname() . '/.dirty'));
    }
    $project['composer']->require = $requires;

    $json = json_encode($project['composer'], JSON_UNESCAPED_SLASHES|JSON_PRETTY_PRINT);
    file_put_contents($project['filename'], preg_replace('/^(  +?)\\1(?=[^ ])/m', '$1', $json));

    echo PHP_EOL;
}
