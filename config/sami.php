<?php

use
    /** @noinspection PhpUndefinedClassInspection */
    Sami\Sami;
use
    /** @noinspection PhpUndefinedClassInspection */
    Sami\RemoteRepository\GitHubRemoteRepository;

use /** @noinspection PhpUndefinedClassInspection */
    Symfony\Component\Finder\Finder;

$dir = dirname(__DIR__);

/** @noinspection PhpUndefinedClassInspection, PhpUndefinedMethodInspection */
$iterator = Finder::create()
    ->files()
    ->name('*.php')
    ->exclude('docs')
    ->exclude('config')
    ->exclude('resources')
    ->exclude('vendor')
    ->exclude('tests')
    ->in($dir);

/** @noinspection PhpUndefinedClassInspection */
$options = [
    'theme' => 'default',
    'title' => 'Simples',
    'build_dir' => $dir . '/.docs/html',
    'cache_dir' => $dir . '/.docs/cache',
    'remote_repository'    => new GitHubRemoteRepository('phpzm/core', $dir),
    'default_opened_level' => 2,
];

/** @noinspection PhpUndefinedClassInspection */
return  new Sami($iterator, $options);
