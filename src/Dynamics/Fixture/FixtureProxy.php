<?php

namespace Box2d\Dynamics\Fixture;


use Box2d\Collision\Collision\AABB;

/// This proxy is used internally to connect fixtures to the broad-phase.
class FixtureProxy
{
    public AABB $aabb;
    public ?Fixture $fixture;
    public int $childIndex;
    public $proxyId;

    public function __construct(AABB $aabb)
    {
        $this->aabb = $aabb;
    }
}
