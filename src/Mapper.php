<?php

namespace Soyuka\Automapper;

use ReflectionNamedType;
use RuntimeException;

class Mapper
{
    /**
     * @var class-string|object $to
     */
    public function map(object $o, object|string|null $to = null): object
    {
        $refl = new \ReflectionClass($o);

        if (!$to) {
            $to = $this->getAttribute($refl, MapTo::class, true)->to;
        }

        $toRefl = new \ReflectionClass($to);
        $mapped = $toRefl->newInstance();

        foreach ($refl->getProperties() as $property) {
            $mapTo = $this->getAttribute($property, MapTo::class)?->to ?? $property->getName();
            if (!$toRefl->hasProperty($mapTo)) {
                continue;
            }

            $mapWith = $this->getAttribute($property, MapWith::class)?->with;
            $v = $o->{$property->getName()};
            $mapped->{$mapTo} = is_callable($mapWith) ? $mapWith($v) : $v;
        }

        return $mapped;
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
