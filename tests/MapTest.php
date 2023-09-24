<?php

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\DataProvider;
use Psr\Container\ContainerInterface;
use Soyuka\Automapper\Mapper;
use Soyuka\Automapper\Tests\Fixtures\A;
use Soyuka\Automapper\Tests\Fixtures\B;
use Symfony\Component\PropertyAccess\PropertyAccess;

class MapTest extends TestCase
{
    #[DataProvider('mapProvider')]
    public function testMap($expect, $args, array $deps = []): void
    {
        $mapper = new Mapper(...$deps);
        $this->assertEquals($expect, $mapper->map(...$args));
    }

    public static function mapProvider()
    {
        $a = new A();
        $a->foo = 'test';
        $a->transform = 'test';
        $a->baz = 'me';
        $a->notinb = 'test';

        $b = new B('test');
        $b->transform = 'TEST';
        $b->baz = 'me';
        $b->nomap = true;
        $b->concat = 'testme';
        yield [$b, [$a]];

        $c = clone $b;
        $c->id = 1;
        yield [$c, [$a, $c]];

        $d = clone $b;
        // with propertyAccessor we call the getter
        $d->concat = 'shouldtestme';

        yield [$d, [$a], [PropertyAccess::createPropertyAccessor()]];

        $e = clone $b;
        $e->transform = 'Test';
        $serviceLocator = static::createStub(ContainerInterface::class);
        $serviceLocator->method('has')->willReturnCallback(function ($v): bool {
            return $v === 'strtoupper';
        });
        $serviceLocator->method('get')->with('strtoupper')->willReturn(fn ($v) => ucfirst($v));

        yield [$e, [$a], [null, $serviceLocator]];
    }
}
