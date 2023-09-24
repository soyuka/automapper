<?php

namespace Soyuka\Automapper\Attributes;

use Attribute;

#[Attribute(Attribute::TARGET_CLASS | Attribute::TARGET_PROPERTY)]
final class MapTo
{
    public function __construct(public string $to)
    {
    }
}
