<?php

namespace Box2d\Dynamics;


use Box2d\Common\Math\Vec2;

class BodyDef
{
    /// The body type.
    /// static: zero mass, zero velocity, may be manually moved
    /// kinematic: zero mass, non-zero velocity set by user, moved by solver
    /// dynamic: positive mass, non-zero velocity determined by forces, moved by solver
    public const TYPE_STATIC_BODY = 0;
    public const TYPE_KINEMATIC_BODY = 1;
    public const TYPE_DYNAMIC_BODY = 2;

    /// The body type: static, kinematic, or dynamic.
    /// Note: if a dynamic body would have zero mass, the mass is set to one.
    public int $type;

    /// The world position of the body. Avoid creating bodies at the origin
    /// since this can lead to many overlapping shapes.
    public Vec2 $position;

    /// The world angle of the body in radians.
    public float $angle;

    /// The linear velocity of the body's origin in world co-ordinates.
    public Vec2 $linearVelocity;

    /// The angular velocity of the body.
    public float $angularVelocity;

    /// Linear damping is use to reduce the linear velocity. The damping parameter
    /// can be larger than 1.0f but the damping effect becomes sensitive to the
    /// time step when the damping parameter is large.
    /// Units are 1/time
    public float $linearDamping;

    /// Angular damping is use to reduce the angular velocity. The damping parameter
    /// can be larger than 1.0f but the damping effect becomes sensitive to the
    /// time step when the damping parameter is large.
    /// Units are 1/time
    public float $angularDamping;

    /// Set this flag to false if this body should never fall asleep. Note that
    /// this increases CPU usage.
    public bool $allowSleep;

    /// Is this body initially awake or sleeping?
    public bool $awake;

    /// Should this body be prevented from rotating? Useful for characters.
    public bool $fixedRotation;

    /// Is this a fast moving body that should be prevented from tunneling through
    /// other moving bodies? Note that all bodies are prevented from tunneling through
    /// kinematic and static bodies. This setting is only considered on dynamic bodies.
    /// @warning You should use this flag sparingly since it increases processing time.
    public bool $bullet;

    /// Does this body start out enabled?
    public bool $enabled;

    /// Use this to store application specific body data.
    /** @var mixed|null */
    public $userData;

    /// Scale the gravity applied to this body.
    public float $gravityScale;

    public function __construct()
    {
        $this->userData = null;
        $this->position = new Vec2(0,0);
        $this->angle = 0;
        $this->linearVelocity = new Vec2(0,0);
        $this->angularVelocity = 0;
        $this->linearDamping = 0;
        $this->angularDamping = 0;
        $this->allowSleep = true;
        $this->awake = true;
        $this->fixedRotation = false;
        $this->bullet = false;
        $this->type = self::TYPE_STATIC_BODY;
        $this->gravityScale = 1;
    }
}
