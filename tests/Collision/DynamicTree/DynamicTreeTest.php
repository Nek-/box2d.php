<?php

namespace Collision\DynamicTree;

use Box2d\Collision\Collision\AABB;
use Box2d\Collision\DynamicTree\DynamicTree;
use Box2d\Common\Math\Vec2;
use PHPUnit\Framework\TestCase;
use Webmozart\Assert\InvalidArgumentException;

class DynamicTreeTest extends TestCase
{
    public function testItsImpossibleToMoveAProxyThatIsNotALeaf()
    {
        $this->expectException(InvalidArgumentException::class);
        $tree = new DynamicTree();

        $id = $tree->CreateProxy(new AABB(), 'some user data');
        $id2 = $tree->CreateProxy(new AABB(), 'something else');

        $tree->MoveProxy($id, new AABB(), new Vec2(1,1));
    }

    public function testLeafInsertionAndRemoval()
    {
        $tree = new DynamicTree();

        $id = $tree->CreateProxy(new AABB(), 'some user data');
        $id2 = $tree->CreateProxy(new AABB(), 'something else');

        $tree->MoveProxy($id2, new AABB(), new Vec2(1,1));
        $tree->MoveProxy($id, new AABB(), new Vec2(1,1));
    }
}
