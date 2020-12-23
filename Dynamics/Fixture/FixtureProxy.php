<?php

namespace Box2d\Dynamics\Fixture;


use Box2d\Collision\Collision\AABB;

class FixtureProxy
{
    public AABB $aabb;
    public ?Fixture $fixture;
    public int $childIndex;
    public $proxyId;

    public function __construct()
    {
        $this->aabb = new AABB();
    }
}
