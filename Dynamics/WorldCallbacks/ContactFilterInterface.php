<?php

namespace Box2d\Dynamics\WorldCallbacks;


use Box2d\Dynamics\Fixture\Fixture;

interface ContactFilterInterface
{
    public function ShouldCollide(Fixture $fixtureA, Fixture $fixtureB): bool;
}
