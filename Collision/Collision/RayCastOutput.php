<?php

namespace Box2d\Collision\Collision;


use Box2d\Common\Math\Vec2;

/// Ray-cast output data. The ray hits at p1 + fraction * (p2 - p1), where p1 and p2
/// come from b2RayCastInput.
class RayCastOutput
{
    public Vec2 $normal;
    public float $fraction;
}
