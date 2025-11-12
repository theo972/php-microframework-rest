<?php

namespace App\Core;

use ReflectionClass;
use ReflectionNamedType;

final class Autowire
{
    private array $instances;
    public function __construct(array $preloadedInstances = [])
    {
        $this->instances = $preloadedInstances;
    }

    public function make(string $className): object
    {
        if (isset($this->instances[$className])) {
            return $this->instances[$className];
        }

        $reflectionClass = new ReflectionClass($className);
        $constructor = $reflectionClass->getConstructor();

        if ($constructor === null || $constructor->getNumberOfParameters() === 0) {
            $object  = $reflectionClass->newInstance();
            return $this->instances[$className] = $object;
        }

        $constructorArgs = [];
        foreach ($constructor->getParameters() as $parameter) {
            $type = $parameter->getType();

            if ($type instanceof ReflectionNamedType && !$type->isBuiltin()) {
                /** @var class-string $dependencyClass */
                $dependencyClass   = $type->getName();
                $constructorArgs[] = $this->make($dependencyClass);
                continue;
            }

            if ($parameter->isDefaultValueAvailable()) {
                $constructorArgs[] = $parameter->getDefaultValue();
                continue;
            }

            throw new \LogicException(sprintf(
                'Autowire: unable to resolve %s::$%s (type: %s)',
                $className,
                $parameter->getName(),
                $type?->getName() ?? 'none'
            ));
        }

        $object = $reflectionClass->newInstanceArgs($constructorArgs);
        return $this->instances[$className] = $object;
    }

    public function set(string $className, object $instance): void
    {
        $this->instances[$className] = $instance;
    }
}
