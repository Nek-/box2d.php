<?php

namespace Box2d\Collision\Collision;


use Box2d\Common\Math\Vec2;

/// Ray-cast input data. The ray extends from p1 to p1 + maxFraction * (p2 - p1).
class RayCastInput
{
    public Vec2 $p1;
    public Vec2 $p2;
    public float $maxFraction;
}
