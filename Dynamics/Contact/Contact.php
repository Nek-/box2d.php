<?php

namespace Box2d\Dynamics\Contact;


use Box2d\Collision\Collision\Manifold;
use Box2d\Dynamics\Fixture\Fixture;

class Contact
{
    // Flags stored in flags
    public const ISLAND_FLAG = 0x1;
    public const TOUCHING_FLAG = 0x2;
    public const ENABLED_FLAG = 0x4;
    public const FILTER_FLAG = 0x8;
    public const BULLET_HIT_FLAG = 0x10;
    public const TOI_FLAG = 0x20;


    public int $flags;

    // Nodes for connecting bodies.
    public ContactEdge $nodeA;
    public ContactEdge $nodeB;

    public Fixture $fixtureA;
    public Fixture $fixtureB;

    public int $indexA;
    public int $indexB;

    public Manifold $manifold;

    public int $toiCount;
    public float $toi;

    public float $friction;
    public float $restitution;
    public float $restitutionThreshold;

    public float $tangentSpeed;
}
