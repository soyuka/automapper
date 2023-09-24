<?php

namespace Soyuka\Automapper\Tests\Fixtures;

use Soyuka\Automapper\Attributes\MapIf;
use Soyuka\Automapper\Attributes\MapTo;
use Soyuka\Automapper\Attributes\MapWith;

#[MapTo(B::class)]
class A
{
    #[MapTo('bar')]
    public string $foo;

    public string $baz;

    public string $notinb;

    #[MapWith('strtoupper')]
    public string $transform;

    #[MapWith([A::class, 'concatFn'])]
    public ?string $concat = null;

    #[MapIf('boolval')]
    public bool $nomap = false;

    public function getConcat()
    {
        return 'should';
    }

    public static function concatFn($v, $object): string
    {
        return $v . $object->foo . $object->baz;
    }
}
