<?php

namespace Soyuka\Automapper\Tests\Fixtures;

use Soyuka\Automapper\Attributes\MapTo;

#[MapTo(D::class)]
class C
{
    public function __construct(#[MapTo('baz')] public string $foo, #[MapTo('bat')] public string $bar)
    {
    }
}
