<?php

namespace Box2d\Common\Math;


class Sweep
{
    ///< local center of mass position
    public Vec2 $localCenter;

    ///< center world positions
    public Vec2 $c0;
    public Vec2 $c;

    ///< world angles
    public float $a0;
    public float $a;

    /// Fraction of the current time step in the range [0,1]
    /// c0 and a0 are the positions at alpha0.
    public float $alpha0;

    public function __construct()
    {
        $this->localCenter = new Vec2();
    }
}
