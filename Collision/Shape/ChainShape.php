<?php

namespace Box2d\Collision\Shape;


use Box2d\Collision\Collision\AABB;
use Box2d\Collision\Collision\RayCastInput;
use Box2d\Collision\Collision\RayCastOutput;
use Box2d\Common\Math\Transform;
use Box2d\Common\Math\Vec2;
use Box2d\Common\Settings;
use Webmozart\Assert\Assert;

class ChainShape extends Shape
{
    /** @var Vec2[] */
    private array $vertices;

    public function GetType(): int
    {
        // TODO: Implement GetType() method.
        throw new \Exception('Not implemented yet');
    }

    public function GetChildCount(): int
    {
        // edge count = vertex count - 1
        return \count($this->vertices) - 1;
    }

    public function CreateLoop(array $vertices)
    {
        Assert::isEmpty($this->vertices);
        Assert::minCount($vertices, 3);

        $count = \count($vertices);
        for ($i = 1; $i < $count; ++$i) {
            $v1 = $vertices[$i-1];
            $v2 = $vertices[$i];
            Assert::true(Vec2::DistanceSquared($v1, $v2) > Settings::linearSlop * Settings::linearSlop, 'If the code crashes here, it means your vertices are too close together.');
        }

        foreach ($vertices as $vertex) {
            $this->vertices[] = clone $vertex;
        }
    }

    public function TestPoint(Transform $xf, Vec2 $p): bool
    {
        throw new \Exception('not writted yet');
    }

    public function RayCast(RayCastOutput $output, RayCastInput $input, Transform $transform, int $childIndex): bool
    {
        throw new \Exception('not writted yet');
    }

    public function ComputeAABB(AABB $aabb, Transform $xf, int $childIndex): void
    {
        throw new \Exception('not writted yet');
    }

    public function ComputeMass(MassData $massData, float $density): void
    {
        $massData->mass = 0.0;
        $massData->center->SetZero();
        $massData->I = 0.0;
    }

    public function __clone()
    {
        $vertices = [];
        foreach ($vertices as $vertex) {
            $this->vertices[] = clone $vertex;
        }
        $shape = new ChainShape();
        $shape->vertices = $vertices;

        return $shape;
    }

//    public function Clear() {/* @TODO */}
//    public function CreateLoop(Vec2 $vec2, int $count) {/* @TODO */}
}
