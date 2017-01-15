<?php

namespace Simples\Core\Kernel;


/**
 * Class Container
 * @package Simples\Core\Kernel
 */
class Container
{
    /**
     * @var Container Instance of Container container
     */
    protected static $instance;

    /**
     * @var array List of IoC Bindings, empty array for default
     */
    protected $bindings = [];

    /**
     * Container constructor.
     *
     * Constructor is protected so people can never
     * do "new Container()"
     */
    protected function __construct()
    {
        //
    }

    /**
     * @return Container Current Container container instance
     */
    public static function getInstance()
    {
        // if there is not a instance yet, create a new one
        if (null === self::$instance) {
            self::$instance = new self();
        }

        // return the new or already existing instance
        return self::$instance;
    }

    /**
     * Register a class or alias into the Container.
     *
     * @param $alias
     * @param $implementation
     * @return $this
     */
    public function register($alias, $implementation)
    {
        $this->bindings[$alias] = $implementation;

        return $this;
    }

    /**
     * UnRegister a Interface/Class/Alias.
     *
     * @param $aliasOrClassName
     * @return $this
     */
    public function unRegister($aliasOrClassName)
    {
        if (array_key_exists($aliasOrClassName, $this->bindings)) {
            unset($this->bindings[$aliasOrClassName]);
        }

        return $this;
    }

    /**
     * Resolves and created a new instance of a desired class.
     *
     * @param $alias
     * @return mixed
     */
    public function make($alias)
    {
        if (array_key_exists($alias, $this->bindings)) {
            $classOrObject = $this->bindings[$alias];

            if (is_object($classOrObject)) {
                return $classOrObject;
            }

            return $this->makeInstance($classOrObject);
        }

        if (class_exists($alias)) {
            return self::register($alias, $this->makeInstance($alias))->make($alias);
        }

        return null;
    }

    /**
     * Created a instance of a desired class.
     *
     * @param $className
     * @return mixed
     */
    protected function makeInstance($className)
    {
        // class reflection
        $reflection = new \ReflectionClass($className);
        // get the class constructor
        $constructor = $reflection->getConstructor();

        // if there is no constructor, just create and
        // return a new instance
        if (!$constructor) {
            return $reflection->newInstance();
        }

        // created and returns the new instance passing the
        // resolved parameters
        return $reflection->newInstanceArgs($this->resolveParameters($constructor->getParameters(), []));
    }

    /**
     * @param $instance
     * @param $method
     * @param $parameters
     * @param bool $labels
     * @return array
     */
    public function resolveMethodParameters($instance, $method, $parameters, $labels = false)
    {
        // method reflection
        $reflectionMethod = new \ReflectionMethod($instance, $method);

        // resolved array of parameters
        return $this->resolveParameters($reflectionMethod->getParameters(), $parameters, $labels);
    }

    /**
     * @param $callable
     * @param $parameters
     * @param bool $labels
     * @return array
     */
    public function resolveFunctionParameters($callable, $parameters, $labels = false)
    {
        // method reflection
        $reflectionFunction = new \ReflectionFunction($callable);

        // resolved array of parameters
        return $this->resolveParameters($reflectionFunction->getParameters(), $parameters, $labels);
    }

    /**
     * @param $parameters
     * @param $data
     * @param bool $labels
     * @return array
     */
    private function resolveParameters($parameters, $data, $labels = false)
    {
        $parametersToPass = [];

        /** @var \ReflectionParameter $reflectionParameter */
        foreach ($parameters as $reflectionParameter) {

            $parameterClassName = isset($reflectionParameter->getClass()->name) ? $reflectionParameter->getClass()->name : '';

            if ($parameterClassName) {

                $parametersToPass[] = self::make($parameterClassName);

            } else if (isset($data[$reflectionParameter->getName()]) || count($data)) {

                $parameter = null;
                if ($labels && isset($data[$reflectionParameter->getName()])) {
                    $parameter = $data[$reflectionParameter->getName()];
                    unset($data[$reflectionParameter->getName()]);
                }
                if (!$parameter) {
                    $parameter = $data[0];
                    array_shift($data);
                    reset($data);
                }
                $parametersToPass[] = $parameter;

            } else {

                $parametersToPass[] = null;
            }
        }

        return $parametersToPass;
    }
}