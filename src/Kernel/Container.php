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
        // resolved array of parameters
        $parametersToPass = [];

        // for each expected parameter,
        // go through the container and resolve it
        /** @var \ReflectionParameter $reflectionParameter */
        foreach ($parameters as $reflectionParameter) {

            // get the expected class
            $parameterClassName = isset($reflectionParameter->getClass()->name) ? $reflectionParameter->getClass()->name : '';

            // if there is a class
            if ($parameterClassName) {
                // ask the container to resolve it
                $parametersToPass[] = self::make($parameterClassName);

            } else if ($labels && isset($data[$reflectionParameter->getName()])) {
                // get parameter by name
                $parametersToPass[] = $data[$reflectionParameter->getName()];
                // remove from list
                unset($data[$reflectionParameter->getName()]);

            } else if (!$labels && count($data)) {

                // add a null info to complete arguments
                $parametersToPass[] = $data[0];
                // remove the first
                array_shift($data);
                // reconfigure the array
                reset($data);

            } else {
                // send null to fill
                $parametersToPass[] = null;
            }
        }

        return $parametersToPass;
    }
}