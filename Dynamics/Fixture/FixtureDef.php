<?php

namespace Box2d\Dynamics\Fixture;

use Box2d\Collision\Shape\Shape;
use Box2d\Settings;

class FixtureDef
{
    public ?Shape $shape;
    public float $friction;
    public float $restitution;
    public float $restitutionThreshold;
    public float $density;
    public bool $isSensor;
    public Filter $filter;

    /** @var mixed|null */
    public $userData;

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
