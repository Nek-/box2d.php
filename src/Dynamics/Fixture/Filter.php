<?php

namespace Box2d\Dynamics\Fixture;


/// This holds contact filtering data.
class Filter
{
    /// The collision category bits. Normally you would just set one bit.
    public int $categoryBits;

    /// The collision mask bits. This states the categories that this
    /// shape would accept for collision.
    public int $maskBits;

    /// Collision groups allow a certain group of objects to never collide (negative)
    /// or always collide (positive). Zero means no collision group. Non-zero group
    /// filtering always wins against the mask bits.
    public int $groupIndex;

    public function __construct()
    {
        $this->categoryBits = 0x0001;
        $this->maskBits = 0xFFFF;
        $this->groupIndex = 0;
    }
}
