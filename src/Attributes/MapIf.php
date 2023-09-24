<?php

namespace Soyuka\Automapper\Attributes;

use Attribute;

#[Attribute(Attribute::TARGET_PROPERTY)]
final class MapIf
{
    /**
     * @param callable $if
     */
    public function __construct(public mixed $if)
    {
    }
}
