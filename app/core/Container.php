<?php

namespace App\Core;

use \Exception;

class Container
{
    /**
     * Array para armazenar as instâncias dos serviços
     * @var array
     */
    private static $instances = [];

    /**
     * Array para armazenar as definições dos serviços
     * @var array
     */
    private static $bindings = [];

    /**
     * Array para armazenar singletons
     * @var array
     */
    private static $singletons = [];

    /**
     * Registra um serviço no container
     * @param string $abstract
     * @param callable|string $concrete
     * @param bool $singleton
     */
    public static function bind($abstract, $concrete = null, $singleton = false)
    {
        if (is_null($concrete)) {
            $concrete = $abstract;
        }

        self::$bindings[$abstract] = [
            'concrete' => $concrete,
            'singleton' => $singleton
        ];
    }

    /**
     * Registra um singleton no container
     * @param string $abstract
     * @param callable|string $concrete
     */
    public static function singleton($abstract, $concrete = null)
    {
        self::bind($abstract, $concrete, true);
    }

    /**
     * Resolve uma dependência do container
     * @param string $abstract
     * @return mixed
     * @throws Exception
     */
    public static function resolve($abstract)
    {
        // Se é um singleton e já foi instanciado, retorna a instância
        if (isset(self::$singletons[$abstract])) {
            return self::$singletons[$abstract];
        }

        // Se não está registrado, tenta resolver automaticamente
        if (!isset(self::$bindings[$abstract])) {
            return self::build($abstract);
        }

        $binding = self::$bindings[$abstract];
        $concrete = $binding['concrete'];

        // Se é uma closure
        if (is_callable($concrete)) {
            $instance = $concrete();
        } else {
            $instance = self::build($concrete);
        }

        // Se é singleton, armazena a instância
        if ($binding['singleton']) {
            self::$singletons[$abstract] = $instance;
        }

        return $instance;
    }

    /**
     * Constrói uma instância da classe
     * @param string $concrete
     * @return mixed
     * @throws Exception
     */
    private static function build($concrete)
    {
        if (!class_exists($concrete)) {
            throw new Exception("Class {$concrete} not found");
        }

        $reflector = new \ReflectionClass($concrete);

        if (!$reflector->isInstantiable()) {
            throw new Exception("Class {$concrete} is not instantiable");
        }

        $constructor = $reflector->getConstructor();

        if (is_null($constructor)) {
            return new $concrete;
        }

        $parameters = $constructor->getParameters();
        $dependencies = [];

        foreach ($parameters as $parameter) {
            $type = $parameter->getType();

            if (is_null($type)) {
                if ($parameter->isDefaultValueAvailable()) {
                    $dependencies[] = $parameter->getDefaultValue();
                } else {
                    throw new Exception("Cannot resolve parameter \${$parameter->getName()}");
                }
            } elseif ($type instanceof \ReflectionNamedType && !$type->isBuiltin()) {
                $className = $type->getName();
                $dependencies[] = self::resolve($className);
            } else {
                throw new Exception("Cannot resolve parameter \${$parameter->getName()} of unsupported type");
            }
        }


        return $reflector->newInstanceArgs($dependencies);
    }

    /**
     * Registra uma instância específica no container
     * @param string $abstract
     * @param mixed $instance
     */
    public static function instance($abstract, $instance)
    {
        self::$singletons[$abstract] = $instance;
    }

    /**
     * Verifica se um serviço está registrado
     * @param string $abstract
     * @return bool
     */
    public static function bound($abstract)
    {
        return isset(self::$bindings[$abstract]) || isset(self::$singletons[$abstract]);
    }

    /**
     * Remove um serviço do container
     * @param string $abstract
     */
    public static function forget($abstract)
    {
        unset(self::$bindings[$abstract], self::$singletons[$abstract]);
    }

    /**
     * Limpa todos os serviços do container
     */
    public static function flush()
    {
        self::$bindings = [];
        self::$singletons = [];
        self::$instances = [];
    }
}

