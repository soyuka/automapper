<?php

namespace Soyuka\Automapper\Tests\Fixtures;

class B
{
    public function __construct(private string $bar)
    {
    }
    public string $baz;
    public string $transform;
    public string $concat;
    public bool $nomap = true;
    public int $id;
}
