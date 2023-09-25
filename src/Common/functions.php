<?php

namespace Box2d\Common;

use Box2d\Common\Math\Vec2;

function IsValid(float $x)
{
    return is_finite($x);
}

function Cross(Vec2 $a, Vec2 $b): float
{
    return $a->x * $b->y - $a->y * $b->x;
}
