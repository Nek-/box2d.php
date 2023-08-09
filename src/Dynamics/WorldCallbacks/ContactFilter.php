<?php

namespace Box2d\Dynamics\WorldCallbacks;


use Box2d\Dynamics\Fixture\Fixture;

class ContactFilter implements ContactFilterInterface
{
    public static function getDefaultContactFilter(): ContactFilterInterface
    {
        return new self;
    }

    public function ShouldCollide(Fixture $fixtureA, Fixture $fixtureB): bool
    {
        $filterA = $fixtureA->GetFilterData();
        $filterB = $fixtureB->GetFilterData();

        if ($filterA->groupIndex == $filterB->groupIndex && $filterA->groupIndex !== 0) {
            return $filterA->groupIndex > 0;
        }

        $collide = ($filterA->maskBits & $filterB->categoryBits) !== 0 && ($filterA->categoryBits & $filterB->maskBits) !== 0;

        return $collide;
    }
}
