<?php

namespace Soyuka\Automapper\Tests\Fixtures;

use Soyuka\Automapper\MapTo;
use Soyuka\Automapper\MapWith;

#[MapTo(B::class)]
class A
{
    #[MapTo('bar')]
    public string $foo;

    public string $baz;

    public string $notinb;

    #[MapWith('strtoupper')]
    public string $transform;
}
