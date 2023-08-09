<?php

namespace Box2d\Common\Math;


class Rot
{
    public float $s;
    public float $c;

    /// Set using an angle in radians.
    public function Set(float $angle)
    {
        /// TODO_ERIN optimize
        $this->s = sin($angle);
        $this->c = cos($angle);
    }
}
