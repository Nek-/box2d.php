<?php

namespace Box2d\Collision\DynamicTree;

interface QueryCallbackInterface
{
    public function QueryCallback(int $proxyId): bool;
}
