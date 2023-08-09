<?php

namespace Box2d\Dynamics\Fixture;

use Box2d\Collision\Shape\Shape;
use Box2d\Settings;


/// A fixture definition is used to create a fixture. This class defines an
/// abstract fixture definition. You can reuse fixture definitions safely.
class FixtureDef
{
    /// The shape, this must be set. The shape will be cloned, so you
    /// can create the shape on the stack.
    public ?Shape $shape;

    /// Use this to store application specific fixture data.
    public mixed $userData;

    /// The friction coefficient, usually in the range [0,1].
    public float $friction;

    /// The restitution (elasticity) usually in the range [0,1].
    public float $restitution;

    /// Restitution velocity threshold, usually in m/s. Collisions above this
    /// speed have restitution applied (will bounce).
    public float $restitutionThreshold;

    /// The density, usually in kg/m^2.
    public float $density;

    /// A sensor shape collects contact information but never generates a collision
    /// response.
    public bool $isSensor;

    /// Contact filtering data.
    public Filter $filter;

    public function __construct()
    {
        $this->shape = null;
        $this->userData = null;
        $this->friction = 0.2;
        $this->restitution = 0.0;
        $this->restitutionThreshold = 1.0 * Settings::lengthUnitsPerMeter;
        $this->density = 0.0;
        $this->isSensor = false;
        $this->filter = new Filter();
    }
}
