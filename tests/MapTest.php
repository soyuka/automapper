<?php

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\DataProvider;
use Psr\Container\ContainerInterface;
use Soyuka\Automapper\Mapper;
use Soyuka\Automapper\Tests\Fixtures\A;
use Soyuka\Automapper\Tests\Fixtures\B;
use Soyuka\Automapper\Tests\Fixtures\C;
use Soyuka\Automapper\Tests\Fixtures\D;
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
        $d = new D(baz: 'foo', bat: 'bar');
        $c = new C(foo: 'foo', bar: 'bar');
        $a = new A();
        $a->foo = 'test';
        $a->transform = 'test';
        $a->baz = 'me';
        $a->notinb = 'test';
        $a->relation = $c;
        $a->relationNotMapped = $d;

        $b = new B('test');
        $b->transform = 'TEST';
        $b->baz = 'me';
        $b->nomap = true;
        $b->concat = 'testme';
        $b->relation = $d;
        $b->relationNotMapped = $d;
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
