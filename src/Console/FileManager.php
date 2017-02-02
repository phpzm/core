<?php
/**
 * Created by PhpStorm.
 * User: Ézio
 * Date: 30/01/2017
 * Time: 21:10
 */

namespace Simples\Core\Console;

use Simples\Core\Kernel\App;
use Simples\Core\Helper\File;

class FileManager
{
    private $class;

    private $directory;

    private $stream;

    private $replacements;

    public function __construct($class, $replacements)
    {

        $this->replacements = $replacements;

        $this->directory = getcwd();

        $this->class = $class;

        return $this;
    }


    public function execute($layer = 'all')
    {
        switch ($layer) {
            case 'model':
                $this->stream = ['model' => File::read(App::$ROOT . '/' . TEMPLATE_DIR . 'model.tpl')];
                break;

            case 'controller':
                $this->stream = ['controller' => File::read(App::$ROOT . '/' . TEMPLATE_DIR . 'controller.tpl')];
                break;

            case 'repository':
                $this->stream = ['repository' => File::read(App::$ROOT . '/' . TEMPLATE_DIR . 'repository.tpl')];
                break;

            case 'all':
                $this->stream = [
                    'model' => File::read(App::$ROOT . '/' . TEMPLATE_DIR . 'model.tpl'),
                    'controller' => File::read(App::$ROOT . '/' . TEMPLATE_DIR . 'controller.tpl'),
                    'repository' => File::read(App::$ROOT . '/' . TEMPLATE_DIR . 'repository.tpl')
                ];
                break;
        }

        if ($this->stream != null && is_array($this->stream)) {
            $this->stream = $this->replace($this->stream, $this->replacements);
            $this->save();
        }
    }

    private function save()
    {
        $class = $this->class;
        $directory = $this->directory;
        $stream = $this->stream;

        foreach ($stream as $key => $layer) {
            $layer_dir = ucwords($key);
            if (!file_exists($directory . '/' . $layer_dir)) {
                $sucess = mkdir($directory . "/" . $layer_dir, 0755);
            } else
                $sucess = true;

            if ($sucess) {

                File::write($directory . '\\' . $layer_dir . '/' . $class . (($key !== 'model') ? $layer_dir : '') . '.php', $layer);

            }

        }
        return false;
    }


    private function replace($stream, $replacements)
    {
        foreach ($stream as $layer => &$content) {
            foreach ($replacements as $field) {
                $content = str_replace($field['field'], $field['value'], $content);
            }
        }
        return $stream;
    }


}