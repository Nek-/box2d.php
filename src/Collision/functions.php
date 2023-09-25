<?php

namespace Box2d\Collision;

use Box2d\Collision\Collision\AABB;
use Box2d\Common\Math\Vec2;

function b2TestOverlap(AABB $a,AABB $b) {
    $d1 = new Vec2();
    $d1->x = $b->lowerBound->x - $a->upperBound->x;
    $d1->y = $b->lowerBound->y - $a->upperBound->y;

    $d2 = new Vec2();
    $d2->x = $a->lowerBound->x - $b->upperBound->x;
    $d2->y = $a->lowerBound->y - $b->upperBound->y;

    if ($d1->x > 0.0 || $d1->y > 0.0) {
        return false;
    }

    if ($d2->x > 0.0 || $d2->y > 0.0) {
        return false;
    }

    return true;
}
