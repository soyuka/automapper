<?php

namespace Soyuka\Automapper;

use Psr\Container\ContainerInterface;
use RuntimeException;
use Soyuka\Automapper\Attributes\MapIf;
use Soyuka\Automapper\Attributes\MapTo;
use Soyuka\Automapper\Attributes\MapWith;
use Symfony\Component\PropertyAccess\PropertyAccessorInterface;

class Mapper
{
    public function __construct(private ?PropertyAccessorInterface $propertyAccessor = null, private ?ContainerInterface $serviceLocator = null)
    {
    }

    /**
     * @var class-string|object $to
     */
    public function map(object $object, object|string|null $to = null): object
    {
        $refl = new \ReflectionClass($object);

        if (!$to) {
            $to = $this->getAttribute($refl, MapTo::class, true)->to;
        }

        $arguments = [];
        if (is_object($to)) {
            $toRefl = new \ReflectionClass(get_class($to));
            $mapped = $to;
        } else {
            $toRefl = new \ReflectionClass($to);
            $mapped = $toRefl->newInstanceWithoutConstructor();
        }

        $constructor = $toRefl->getConstructor();
        foreach ($constructor?->getParameters() ?? [] as $parameter) {
            $arguments[$parameter->getName()] = $parameter->isDefaultValueAvailable() ? $parameter->getDefaultValue() : null;
        }

        foreach ($refl->getProperties() as $property) {
            $propertyName = $property->getName();
            $mapTo = $this->getAttribute($property, MapTo::class)?->to ?? $propertyName;
            if (!$toRefl->hasProperty($mapTo)) {
                continue;
            }

            $value = $this->propertyAccessor ? $this->propertyAccessor->getValue($object, $propertyName) : $object->{$propertyName};
            $mapIf = $this->getCallable($this->getAttribute($property, MapIf::class)?->if);
            if (is_callable($mapIf) && false === $this->call($mapIf, $value, $object)) {
                continue;
            }

            $mapWith = $this->getCallable($this->getAttribute($property, MapWith::class)?->with);
            if (is_callable($mapWith)) {
                $value = $this->call($mapWith, $value, $object);
            }

            if (array_key_exists($mapTo, $arguments)) {
                $arguments[$mapTo] = $value;
            } else {
                $this->propertyAccessor ? $this->propertyAccessor->setValue($mapped, $mapTo, $value) : ($mapped->{$mapTo} = $value);
            }
        }

        $constructor->invokeArgs($mapped, $arguments);

        return $mapped;
    }

    private function call(callable $fn, mixed $value, object $object)
    {
        $refl = new \ReflectionFunction(\Closure::fromCallable($fn));
        $withParameters = $refl->getParameters();
        $withArgs = [$value];

        // Let's not send object if we don't need to, gives the ability to call native functions
        foreach ($withParameters as $parameter) {
            if ($parameter->getName() === 'object') {
                $withArgs['object'] = $object;
                break;
            }
        }

        return call_user_func_array($fn, $withArgs);
    }

    private function getCallable(string|callable|null $fn)
    {
        if ($this->serviceLocator && is_string($fn) && $this->serviceLocator->has($fn)) {
            return $this->serviceLocator->get($fn);
        }

        return $fn;
    }

    /**
     * @param class-string $name
     */
    private function getAttribute(mixed $refl, string $name, bool $throw = false): mixed
    {
        $a = $refl->getAttributes($name)[0] ?? null;

        if ($throw && !$a) {
            throw new RuntimeException();
        }

        return $a ? $a->newInstance() : $a;
    }
}
