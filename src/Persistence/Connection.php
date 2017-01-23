<?php

namespace Simples\Core\Persistence;

use Simples\Core\Route\Wrapper;
use \Throwable;

/**
 * Class Connection
 * @package Simples\Core\Persistence
 */
abstract class Connection
{
    /**
     * @var mixed
     */
    protected $resource = null;

    /**
     * @var array
     */
    protected $settings = [];

    /**
     * @var array
     */
    protected $logs = [];

    /**
     * Connection constructor.
     * @param array $settings
     */
    public function __construct(array $settings)
    {
        $this->settings = $settings;
    }

    /**
     * @return mixed
     */
    abstract protected function connect();

    /**
     * @return array
     */
    public function getSettings(): array
    {
        return $this->settings;
    }

    /**
     * @return array
     */
    public function getLogs(): array
    {
        return $this->logs;
    }

    /**
     * @param $command
     * @param $parameters
     * @param $logging
     * @return Connection
     */
    public function addLog($command, $parameters, $logging): Connection
    {
        $log = ['command' => $command, 'parameters' => $parameters];
        $this->logs[] = $log;
        if ($logging || Transaction::log()) {
            Wrapper::log($log);
        }
        return $this;
    }
}
