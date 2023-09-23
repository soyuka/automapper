<?php

namespace Soyuka\Automapper;

use Attribute;

#[Attribute(Attribute::TARGET_METHOD | Attribute::TARGET_PROPERTY)]
final class MapWith
{
    public function __construct(public mixed $with)
    {
    }
}
