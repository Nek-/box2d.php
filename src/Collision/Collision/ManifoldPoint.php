<?php

namespace Box2d\Collision\Collision;


use Box2d\Common\Math\Vec2;

/// A manifold point is a contact point belonging to a contact
/// manifold. It holds details related to the geometry and dynamics
/// of the contact points.
/// The local point usage depends on the manifold type:
/// -e_circles: the local center of circleB
/// -e_faceA: the local center of cirlceB or the clip point of polygonB
/// -e_faceB: the clip point of polygonA
/// This structure is stored across time steps, so we keep it small.
/// Note: the impulses are used for internal caching and may not
/// provide reliable contact forces, especially for high speed collisions.
class ManifoldPoint
{
    public Vec2 $localPoint;        ///< usage depends on manifold type
    public float $normalImpulse;    ///< the non-penetration impulse
    public float $tangentImpulse;   ///< the friction impulse
    public ContactID $id;           ///< uniquely identifies a contact point between two shapes
}
