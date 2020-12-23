<?php

namespace Box2d\Collision\Collision;


/// The features that intersect to form the contact point
/// This must be 4 bytes or less.
class ContactFeature
{
    public const TYPE_VERTEX=0;
    public const TYPE_FACE=0;
    public int $indexA;		///< Feature index on shapeA
    public int $indexB;		///< Feature index on shapeB
    public int $typeA;		///< The feature type on shapeA
    public int $typeB;		///< The feature type on shapeB
}
