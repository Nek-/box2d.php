<?php

namespace Box2d\Collision\DynamicTree;


use Box2d\Collision\Collision\AABB;

class TreeNode
{
    /// Enlarged AABB
    public AABB $aabb;

    /** @var mixed|null */
    public $userData;
    public int $child1;
    public int $child2;
    public int $parent;
    public int $next;

    // leaf = 0, free node = -1
    public int $height;

    public bool $moved;

    public function __construct()
    {
        $this->parent = DynamicTree::NULL_NODE;
        $this->next = DynamicTree::NULL_NODE;
        $this->child1 = DynamicTree::NULL_NODE;
        $this->child2 = DynamicTree::NULL_NODE;
        $this->aabb = new AABB();
        $this->height = 0;
        $this->userData = null;
        $this->moved = false;
    }


    public function IsLeaf()
    {
        return $this->child1 === DynamicTree::NULL_NODE;
    }
}
