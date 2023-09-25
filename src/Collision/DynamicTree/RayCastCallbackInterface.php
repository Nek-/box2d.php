<?php

namespace Box2d\Collision\DynamicTree;

use Box2d\Collision\Collision\RayCastInput;

interface RayCastCallbackInterface
{
    public function RayCastCallback(RayCastInput $input, int $proxyId): float;
}
