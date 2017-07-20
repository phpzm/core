<?php

use Sami\Sami;
use Sami\RemoteRepository\AbstractRemoteRepository;
use Symfony\Component\Finder\Finder;

class GitHubPath extends AbstractRemoteRepository
{
    public function getFileUrl($projectVersion, $relativePath, $line)
    {
        $pieces = explode('/', $relativePath);
        $package = $pieces[1];
        unset($pieces[1]);
        $path = implode('/', $pieces);
        $url = 'https://github.com/' . $this->name . '/' . $package .
            '/blob/' . str_replace('\\', '/', $projectVersion . $path);

        if (null !== $line) {
            $url .= '#L' . (int)$line;
        }

        return $url;
    }
}

$dir = dirname(__DIR__, 2);

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
    'build_dir' => $dir . '/core/.docs/html',
    'cache_dir' => $dir . '/core/.docs/cache',
    'remote_repository' => new GitHubPath('phpzm', $dir),
    'default_opened_level' => 2,
];

/** @noinspection PhpUndefinedClassInspection */
return new Sami($iterator, $options);
