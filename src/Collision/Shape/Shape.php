<?php

namespace Box2d\Collision\Shape;


use Box2d\Collision\Collision\AABB;
use Box2d\Collision\Collision\RayCastInput;
use Box2d\Collision\Collision\RayCastOutput;
use Box2d\Common\Math\Transform;
use Box2d\Common\Math\Vec2;

abstract class Shape
{
    public const TYPE_CIRCLE = 0;
    public const TYPE_EDGE = 1;
    public const TYPE_POLYGON = 2;
    public const TYPE_CHAIN = 3;
    public const TYPE_COUNT = 4;

    public int $type;

    /// Radius of a shape. For polygonal shapes this must be b2_polygonRadius. There is no support for
    /// making rounded polygons.
    public float $radius;

    public abstract function GetChildCount(): int;

    /// Test a point for containment in this shape. This only works for convex shapes.
    /// @param xf the shape world transform.
    /// @param p a point in world coordinates.
    public abstract function TestPoint(Transform $xf,Vec2 $p): bool;


    /// Cast a ray against a child shape.
    /// @param output the ray-cast results.
    /// @param input the ray-cast input parameters.
    /// @param transform the transform to be applied to the shape.
    /// @param childIndex the child shape index
    public abstract function RayCast(RayCastOutput $output, RayCastInput $input, Transform $transform, int $childIndex): bool;


    /// Given a transform, compute the associated axis aligned bounding box for a child shape.
    /// @param aabb returns the axis aligned box.
    /// @param xf the world transform of the shape.
    /// @param childIndex the child shape
    public abstract function ComputeAABB(AABB $aabb,Transform $xf, int $childIndex) : void;


    /// Compute the mass properties of this shape using its dimensions and density.
    /// The inertia tensor is computed about the local origin.
    /// @param massData returns the mass data for this shape.
    /// @param density the density in kilograms per meter squared.
    public abstract function ComputeMass(MassData $massData, float $density) : void;

    public abstract function __clone();

    public function GetTYpe()
    {
        return $this->type;
    }

    public function Clone()
    {
        throw new \LogicException('You should use the PHP method to clone this object');
    }
}
