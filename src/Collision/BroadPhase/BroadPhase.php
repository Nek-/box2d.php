<?php

namespace Box2d\Collision\BroadPhase;


use Box2d\Collision\BroadPhase\Pair;
use Box2d\Collision\Collision\AABB;
use Box2d\Collision\DynamicTree\DynamicTree;

class BroadPhase
{
    public const NULL_PROXY = -1;
    //friend class b2DynamicTree;

    private DynamicTree $tree;

    private int $proxyCount;

    /** @var int[] */
    private array $moveBuffer;
//    private int $moveCapacity = 16;
//    private int $moveCount = 0;

    /** @var Pair[]  */
    private array $pairBuffer;
//    private int $pairCapacity = 16;
//    private int $pairCount = 0;

    private int $queryProxyId;

    public function __construct()
    {
        $this->pairBuffer = [];
	    $this->moveBuffer = [];
    }

    public function CreateProxy(AABB $aabb, $userData): int
    {
        $proxyId = $this->tree->CreateProxy($aabb, $userData);
        $this->moveBuffer[] = $proxyId;
        return $proxyId;
    }

//    private function BufferMove(int $proxyId): void {}
//    private function UnBufferMove(int $proxyId): void {}
//
//    private function QueryCallback(int $proxyId): bool {}
}
