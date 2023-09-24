<?php

namespace Soyuka\Automapper\Attributes;

use Attribute;

#[Attribute(Attribute::TARGET_PROPERTY)]
final class MapWith
{
    /**
     * @param callable $with
     */
    public function __construct(public mixed $with)
    {
    }
}
