<?php

namespace Box2d\Common\Math;


class Transform
{
    public Vec2 $p;
    public Rot $q;
    public function __construct(Vec2 $position = null, Rot $rot = null)
    {
        $this->position = $position;
        $this->q = $rot ?? new Rot();
    }
}
