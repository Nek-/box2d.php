<?php

namespace Box2d\Collision\Collision;

/// A manifold for two touching convex shapes.
/// Box2D supports multiple types of contact:
/// - clip point versus plane with radius
/// - point versus point with radius (circles)
/// The local point usage depends on the manifold type:
/// -e_circles: the local center of circleA
/// -e_faceA: the center of faceA
/// -e_faceB: the center of faceB
/// Similarly the local normal usage:
/// -e_circles: not used
/// -e_faceA: the normal on polygonA
/// -e_faceB: the normal on polygonB
/// We store contacts in this way so that position correction can
/// account for movement, which is critical for continuous physics.
/// All contact scenarios must be expressed in one of these types.
/// This structure is stored across time steps, so we keep it small.
use Box2d\src\Common\Math\Vec2;

class Manifold
{
    public const TYPE_CIRCLES = 0;
    public const TYPE_FACE_A = 1;
    public const TYPE_FACE_B = 2;

    /** @var ManifoldPoint[] */
    public array $points = [];

    public Vec2 $localNormal;
    public Vec2 $localPoint;
    public int $type;
}
