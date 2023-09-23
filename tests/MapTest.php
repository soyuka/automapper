<?php

use PHPUnit\Framework\TestCase;
use Soyuka\Automapper\Mapper;
use Soyuka\Automapper\Tests\Fixtures\A;
use Soyuka\Automapper\Tests\Fixtures\B;

class MapTest extends TestCase
{
    public function testMap()
    {
        $mapper = new Mapper();
        $a = new A();
        $a->foo = 'test';
        $a->transform = 'test';
        $a->baz = 'test';
        $a->notinb = 'test';
        $b = new B();
        $b->bar = 'test';
        $b->transform = 'TEST';
        $b->baz = 'test';
        $this->assertEquals($b, $mapper->map($a));
    }
}
