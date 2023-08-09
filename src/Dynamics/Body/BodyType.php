<?php

namespace Box2d\Dynamics;

enum BodyType: int
{
    /// The body type.
    /// static: zero mass, zero velocity, may be manually moved
    /// kinematic: zero mass, non-zero velocity set by user, moved by solver
    /// dynamic: positive mass, non-zero velocity determined by forces, moved by solver
    case TYPE_STATIC_BODY = 0;
    case TYPE_KINEMATIC_BODY = 1;
    case TYPE_DYNAMIC_BODY = 2;
}
