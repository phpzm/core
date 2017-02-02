<?php

namespace Simples\Core\Console;

use Simples\Core\Helper\Directory;
use Simples\Core\Helper\Text;
use Simples\Core\Kernel\App;
use Simples\Core\Helper\File;

/**
 * Class FileManager
 * @package Simples\Core\Console
 */
class FileManager
{
    /**
     * @var string
     */
    private $namespace;

    /**
     * @var string
     */
    private $class;

    /**
     * @var string
     */
    private $directory;

    /**
     * @var array
     */
    private $stream;

    /**
     * @var array
     */
    private $replacements;

    /**
     * @var string
     */
    private $extension = '.tpl';

    /**
     * @var string
     */
    const LAYER_MODEL = 'model';

    /**
     * @var string
     */
    const LAYER_REPOSITORY = 'repository';

    /**
     * @var string
     */
    const LAYER_CONTROLLER = 'controller';

    /**
     * @var string
     */
    const LAYER_ALL = 'all';

    /**
     * FileManager constructor.
     * @param string $namespace
     * @param string $class
     * @param array $replacements
     */
    public function __construct(string $namespace, string $class, array $replacements)
    {
        $this->namespace = $namespace;
        $this->class = $class;
        $this->replacements = $replacements;
        $this->directory = path(true, App::config('app.src'));

        return $this;
    }

    /**
     * Generate streams of layer(s)
     * @param string $layer (self::LAYER_ALL)
     */
    public function execute(string $layer = 'all')
    {
        switch ($layer) {
            case self::LAYER_ALL:
                $streams = [self::LAYER_MODEL, self::LAYER_REPOSITORY, self::LAYER_CONTROLLER];
                break;
            default:
                $streams = [$layer];
        }

        $this->stream = [];
        foreach ($streams as $stream) {
            $template = dirname(__DIR__, 2) . '/' . TEMPLATE_DIR . '/' . $stream . $this->extension;
            $this->stream[$stream] = File::read($template);
        }

        if ($this->stream && is_array($this->stream)) {
            $this->replace();
            $this->save();
        }
    }

    /**
     * Write the template on disk
     * @return bool
     */
    private function save()
    {
        foreach ($this->stream as $key => $file) {
            $layer = ucwords($key);
            $namespace = Text::replace($this->namespace, '\\', '/');
            $root = Text::replace("{$this->directory}/{$namespace}/{$layer}", '//', '/');

            $success = true;
            if (!Directory::exists($root)) {
                $success = Directory::make($root);
            }
            if (!$success) {
                return false;
            }
            $class = $this->class . (($key !== 'model') ? $layer : '');

            $filename = "{$root}/{$class}.php";

            return !!File::write($filename, $file);
        }
        return false;
    }

    /**
     * Replace values on stream template
     * @return array
     */
    private function replace()
    {
        foreach ($this->stream as $layer => &$content) {
            foreach ($this->replacements as $field) {
                $content = Text::replace($content, $field['field'], $field['value']);
            }
        }
        return $this->stream;
    }
}
