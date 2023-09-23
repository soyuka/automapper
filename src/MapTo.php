<?php

namespace Soyuka\Automapper;

use Attribute;

#[Attribute(Attribute::TARGET_CLASS | Attribute::TARGET_METHOD | Attribute::TARGET_PROPERTY)]
final class MapTo
{
    public function __construct(public string $to)
    {
    }
}
