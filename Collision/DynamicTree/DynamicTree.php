<?php

namespace Box2d\Collision\DynamicTree;


/// A dynamic AABB tree broad-phase, inspired by Nathanael Presson's btDbvt.
/// A dynamic tree arranges data in a binary tree to accelerate
/// queries such as volume queries and ray casts. Leafs are proxies
/// with an AABB. In the tree we expand the proxy AABB by b2_fatAABBFactor
/// so that the proxy AABB is bigger than the client object. This allows the client
/// object to move by small amounts without triggering a tree update.
///
/// Nodes are pooled and relocatable, so we use node indices rather than pointers.
use Box2d\Collision\Collision\AABB;
use Box2d\Common\Math\Math;
use Box2d\Common\Math\Vec2;
use Box2d\Common\Settings;
use Webmozart\Assert\Assert;

class DynamicTree
{
    public const NULL_NODE = -1;
    private int $root;

    /** @var TreeNode[] */
    private array $nodes;
//    private int $nodeCount = 0;
//    private int $nodeCapacity = 16;
//    private int $freeList;

    private int $insertionCount;

    public function __construct()
    {
        $this->root = self::NULL_NODE;

        $this->nodes = [];

        // Build a linked list for the free list.
        // 15 is nodeCapacity - 1
        for ($i = 0; $i < 15; ++$i)
        {
            $this->nodes[$i] = new TreeNode();
            $this->nodes[$i]->next = $i + 1;
            $this->nodes[$i]->height = -1;
        }
        $this->nodes[15]->next = self::NULL_NODE;
        $this->nodes[15]->height = -1;
        $this->freeList = 0;

        $this->insertionCount = 0;
    }

    // Create a proxy in the tree as a leaf node. We return the index
    // of the node instead of a pointer so that we can grow
    // the node pool.
    public function CreateProxy(AABB $aabb, $userData): int
    {
        $proxyId = $this->AllocateNode();

        // Fatten the aabb.
        $r_x = Settings::aabbExtension;
        $r_y = Settings::aabbExtension;
        $this->nodes[$proxyId]->aabb->lowerBound->x = $aabb->lowerBound->x - $r_x;
        $this->nodes[$proxyId]->aabb->lowerBound->y = $aabb->lowerBound->y - $r_y;
        $this->nodes[$proxyId]->aabb->upperBound->x = $aabb->upperBound->x + $r_x;
        $this->nodes[$proxyId]->aabb->upperBound->y = $aabb->upperBound->y + $r_y;
        $this->nodes[$proxyId]->userData = $userData;
        $this->nodes[$proxyId]->height = 0;
        $this->nodes[$proxyId]->moved = true;

        $this->InsertLeaf($proxyId);

        return $proxyId;
    }

    private function InsertLeaf(int $leaf)
    {
        ++$this->insertionCount;

        if ($this->root == self::NULL_NODE)
        {
            $this->root = $leaf;
            $this->nodes[$this->root]->parent = self::NULL_NODE;

            return;
        }

        // Find the best sibling for this node
        $leafAABB = $this->nodes[$leaf]->aabb;
        $index = $this->root;
        while ($this->nodes[$index]->IsLeaf() == false)
        {
            $child1 = $this->nodes[$index]->child1;
            $child2 = $this->nodes[$index]->child2;

            $area = $this->nodes[$index]->aabb->GetPerimeter();

            $combinedAABB = new AABB();
            $combinedAABB->Combine($this->nodes[$index]->aabb, $leafAABB);
            $combinedArea = $combinedAABB->GetPerimeter();

            // Cost of creating a new parent for this node and the new leaf
            $cost = 2.0 * $combinedArea;

            // Minimum cost of pushing the leaf further down the tree
            $inheritanceCost = 2.0 * ($combinedArea - $area);

            // Cost of descending into child1
            // $cost1 = 0.0;
            if ($this->nodes[$child1]->IsLeaf())
            {
                $aabb = new AABB();
                $aabb->Combine($leafAABB, $this->nodes[$child1]->aabb);
                $cost1 = $aabb->GetPerimeter() + $inheritanceCost;
            }
            else
            {
                $aabb = new AABB();
                $aabb->Combine($leafAABB, $this->nodes[$child1]->aabb);
                $oldArea = $this->nodes[$child1]->aabb->GetPerimeter();
                $newArea = $aabb->GetPerimeter();
                $cost1 = ($newArea - $oldArea) + $inheritanceCost;
            }

            // Cost of descending into child2
            // $cost2 = 0.0;
            if ($this->nodes[$child2]->IsLeaf())
            {
                $aabb = new AABB();
                $aabb->Combine($leafAABB, $this->nodes[$child2]->aabb);
                $cost2 = $aabb->GetPerimeter() + $inheritanceCost;
            }
            else
            {
                $aabb = new AABB();
                $aabb->Combine($leafAABB, $this->nodes[$child2]->aabb);
                $oldArea = $this->nodes[$child2]->aabb->GetPerimeter();
                $newArea = $aabb->GetPerimeter();
                $cost2 = $newArea - $oldArea + $inheritanceCost;
            }

            // Descend according to the minimum cost.
            if ($cost < $cost1 && $cost < $cost2)
            {
                break;
            }

            // Descend
            if ($cost1 < $cost2)
            {
                $index = $child1;
            }
            else
            {
                $index = $child2;
            }
        }

        $sibling = $index;

        // Create a new parent.
        $oldParent = $this->nodes[$sibling]->parent;
        $newParent = $this->AllocateNode();
        $this->nodes[$newParent]->parent = $oldParent;
        $this->nodes[$newParent]->userData = null;
        $this->nodes[$newParent]->aabb->Combine($leafAABB, $this->nodes[$sibling]->aabb);
        $this->nodes[$newParent]->height = $this->nodes[$sibling]->height + 1;

        if ($oldParent != self::NULL_NODE)
        {
            // The sibling was not the root.
            if ($this->nodes[$oldParent]->child1 == $sibling)
            {
                $this->nodes[$oldParent]->child1 = $newParent;
            }
            else
            {
                $this->nodes[$oldParent]->child2 = $newParent;
            }

            $this->nodes[$newParent]->child1 = $sibling;
            $this->nodes[$newParent]->child2 = $leaf;
            $this->nodes[$sibling]->parent = $newParent;
            $this->nodes[$leaf]->parent = $newParent;
        }
        else
        {
            // The sibling was the root.
            $this->nodes[$newParent]->child1 = $sibling;
            $this->nodes[$newParent]->child2 = $leaf;
            $this->nodes[$sibling]->parent = $newParent;
            $this->nodes[$leaf]->parent = $newParent;
            $this->root = $newParent;
        }

        // Walk back up the tree fixing heights and AABBs
        $index = $this->nodes[$leaf]->parent;
        while ($index != self::NULL_NODE)
        {
            $index = $this->Balance($index);

            $child1 = $this->nodes[$index]->child1;
            $child2 = $this->nodes[$index]->child2;

            Assert::true($child1 != self::NULL_NODE);
            Assert::true($child2 != self::NULL_NODE);

            $this->nodes[$index]->height = 1 + Math::Max($this->nodes[$child1]->height, $this->nodes[$child2]->height);
            $this->nodes[$index]->aabb->Combine($this->nodes[$child1]->aabb, $this->nodes[$child2]->aabb);

            $index = $this->nodes[$index]->parent;
        }

        //Validate();
    }

// Perform a left or right rotation if node A is imbalanced.
// Returns the new root index.
    private function Balance(int $iA): int
    {
        Assert::true($iA != self::NULL_NODE);

        $A = $this->nodes[$iA];
        if ($A->IsLeaf() || $A->height < 2)
        {
            return $iA;
        }

        $iB = $A->child1;
        $iC = $A->child2;
        Assert::keyExists($this->nodes, $iB);
        Assert::keyExists($this->nodes, $iC);

        $B = $this->nodes[$iB];
        $C = $this->nodes[$iC] ;

        $balance = $C->height - $B->height;

        // Rotate C up
        if ($balance > 1)
        {
            $iF = $C->child1;
            $iG = $C->child2;
            Assert::keyExists($this->nodes, $iF);
            Assert::keyExists($this->nodes, $iG);
            $F = $this->nodes[$iF];
            $G = $this->nodes[$iG];

            // Swap A and C
            $C->child1 = $iA;
            $C->parent = $A->parent;
            $A->parent = $iC;

            // A's old parent should point to C
            if ($C->parent != self::NULL_NODE)
            {
                if ($this->nodes[$C->parent]->child1 == $iA)
                {
                    $this->nodes[$C->parent]->child1 = $iC;
                }
                else
                {
                    Assert::true($this->nodes[$C->parent]->child2 == $iA);
                    $this->nodes[$C->parent]->child2 = $iC;
                }
            }
            else
            {
                $this->root = $iC;
            }

            // Rotate
            if ($F->height > $G->height)
            {
                $C->child2 = $iF;
                $A->child2 = $iG;
                $G->parent = $iA;
                $A->aabb->Combine($B->aabb, $G->aabb);
                $C->aabb->Combine($A->aabb, $F->aabb);

                $A->height = 1 + Math::Max($B->height, $G->height);
                $C->height = 1 + Math::Max($A->height, $F->height);
            }
            else
            {
                $C->child2 = $iG;
                $A->child2 = $iF;
                $F->parent = $iA;
                $A->aabb->Combine($B->aabb, $F->aabb);
                $C->aabb->Combine($A->aabb, $G->aabb);

                $A->height = 1 + Math::Max($B->height, $F->height);
                $C->height = 1 + Math::Max($A->height, $G->height);
            }

            return $iC;
        }

        // Rotate B up
        if ($balance < -1)
        {
            $iD = $B->child1;
            $iE = $B->child2;
            Assert::keyExists($this->nodes, $iD);
            Assert::keyExists($this->nodes, $iE);

            $D = $this->nodes[$iD];
            $E = $this->nodes[$iE];

            // Swap A and B
            $B->child1 = $iA;
            $B->parent = $A->parent;
            $A->parent = $iB;

            // A's old parent should point to B
            if ($B->parent != self::NULL_NODE)
            {
                if ($this->nodes[$B->parent]->child1 == $iA)
                {
                    $this->nodes[$B->parent]->child1 = $iB;
                }
                else
                {
                    Assert::true($this->nodes[$B->parent]->child2 == $iA);
                    $this->nodes[$B->parent]->child2 = $iB;
                }
            }else{
                $this->root = $iB;
            }

            // Rotate
            if ($D->height > $E->height)
            {
                $B->child2 = $iD;
                $A->child1 = $iE;
                $E->parent = $iA;
                $A->aabb->Combine($C->aabb, $E->aabb);
                $B->aabb->Combine($A->aabb, $D->aabb);

                $A->height = 1 + Math::Max($C->height, $E->height);
                $B->height = 1 + Math::Max($A->height, $D->height);
            } else {
                $B->child2 = $iE;
                $A->child1 = $iD;
                $D->parent = $iA;
                $A->aabb->Combine($C->aabb, $D->aabb);
                $B->aabb->Combine($A->aabb, $E->aabb);

                $A->height = 1 + Math::Max($C->height, $D->height);
                $B->height = 1 + Math::Max($A->height, $E->height);
            }

            return $iB;
        }

        return $iA;
    }

    private function AllocateNode()
    {
        $id = \count($this->nodes);
        $this->nodes[] = new TreeNode();

        return $id;
    }
}
