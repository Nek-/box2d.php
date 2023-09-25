<?php

namespace Box2d\Collision\BroadPhase;


use Box2d\Collision\BroadPhase\Pair;
use Box2d\Collision\Collision\AABB;
use Box2d\Collision\Collision\RayCastInput;
use Box2d\Collision\DynamicTree\DynamicTree;
use Box2d\Collision\DynamicTree\QueryCallbackInterface;
use Box2d\Common\Math\Vec2;

/**
 * @template T
 */
class BroadPhase implements QueryCallbackInterface
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

    public function DestroyProxy(int $proxyId): void
    {
        // TODO
    }

    /// Call MoveProxy as many times as you like, then when you are done
    /// call UpdatePairs to finalized the proxy pairs (for your time step).
    public function MoveProxy(int $proxyId, AABB $aabb, Vec2 $displacement)
    {
        // TODO
    }

    /// Call to trigger a re-processing of it's pairs on the next call to UpdatePairs.
    public function TouchProxy(int $proxyId)
    {
        // TODO
    }

    /// Get the fat AABB for a proxy.
    public function GetFatAABB(int $proxyId): AABB
    {
        return $this->tree->GetFatAABB($proxyId);
    }

    /// Get user data from a proxy. Returns nullptr if the id is invalid.
    public function GetUserData(int $proxyId): string
    {
        return $this->tree->GetUserData($proxyId);
    }

    /// Test overlap of fat AABBs.
    public function TestOverlap(int $proxyIdA, int $proxyIdB)
    {
        $aabbA = $this->tree->GetFatAABB($proxyIdA);
        $aabbB = $this->tree->GetFatAABB($proxyIdB);

        return testOverlap($aabbA, $aabbB);
    }

    /// Get the number of proxies.
    public function GetProxyCount(): int
    {
        return $this->proxyCount;
    }

    /// Update the pairs. This results in pair callbacks. This can only add pairs.
    /**
     * @param T $callback
     */
    public function UpdatePairs($callback)
    {
        // Perform tree queries for all moving proxies.
        foreach ($this->moveBuffer as $queryProxyId) {
            if (empty($queryProxyId)) {
                continue;
            }

            // We have to query the tree with the fat AABB so that
            // we don't fail to create a pair that may touch later.
            $fatAABB = $this->tree->GetFatAABB($queryProxyId);

            // Query tree, create pairs and add them pair buffer.
            $this->tree->Query($this, $fatAABB);
        }

        // Send pairs to caller
        foreach ($this->pairBuffer as $primaryPair) {
            $userDataA = $this->tree->GetUserData($primaryPair->proxyIdA);
            $userDataB = $this->tree->GetUserData($primaryPair->proxyIdB);

            $callback->AddPair($userDataA, $userDataB);
        }

        // Clear move flags
        foreach ($this->moveBuffer as $proxyId) {
            if (empty($queryProxyId)) {
                continue;
            }

            $this->tree->ClearMoved($proxyId);
        }


        // Reset move buffer
        $this->moveBuffer = [];
    }


    /// Query an AABB for overlapping proxies. The callback class
    /// is called for each proxy that overlaps the supplied AABB.
    /**
     * @param T $callback
     */
    public function Query($callback, AABB $aabb): void
    {
        $this->tree->Query($callback, $aabb);
    }

    /// Ray-cast against the proxies in the tree. This relies on the callback
    /// to perform a exact ray-cast in the case were the proxy contains a shape.
    /// The callback also performs the any collision filtering. This has performance
    /// roughly equal to k * log(n), where k is the number of collisions and n is the
    /// number of proxies in the tree.
    /// @param input the ray-cast input data. The ray extends from p1 to p1 + maxFraction * (p2 - p1).
    /// @param callback a callback class that is called for each proxy that is hit by the ray.
    public function RayCast($callback, RayCastInput $input): void
    {
        $this->tree->RayCast($callback, $input);
    }

    /// Get the height of the embedded tree.
    public function GetTreeHeight(): int
    {
        return $this->tree->GetHeight();
    }

    /// Get the balance of the embedded tree.
    public function GetTreeBalance(): int
    {
        return $this->tree->GetMaxBalance();
    }

    public function GetTreeQuality(): float
    {
        return $this->tree->GetAreaRatio();
    }

    /// Shift the world origin. Useful for large worlds.
    /// The shift formula is: position -= newOrigin
    /// @param newOrigin the new origin with respect to the old origin
    public function ShiftOrigin(Vec2 $newOrigin)
    {
        $this->tree->ShiftOrigin($newOrigin);
    }

    private function BufferMove(int $proxyId): void
    {
        // TODO
    }
    private function UnBufferMove(int $proxyId): void
    {
        // TODO
    }

    public function QueryCallback(int $proxyId): bool
    {
        // TODO
    }
}
