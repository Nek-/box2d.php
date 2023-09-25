<?php

namespace Box2d\Dynamics;


use Box2d\Collision\Shape\MassData;
use Box2d\Collision\Shape\Shape;
use Box2d\Common\Math\Math;
use Box2d\Common\Math\Sweep;
use Box2d\Common\Math\Transform;
use Box2d\Common\Math\Vec2;
use Box2d\Common\World;
use Box2d\Dynamics\Fixture\Fixture;
use Box2d\Dynamics\Fixture\FixtureDef;
use Webmozart\Assert\Assert;

class Body
{
    //friend class b2World;
    //friend class b2Island;
    //friend class b2ContactManager;
    //friend class b2ContactSolver;
    //friend class b2Contact;
    //
    //friend class b2DistanceJoint;
    //friend class b2FrictionJoint;
    //friend class b2GearJoint;
    //friend class b2MotorJoint;
    //friend class b2MouseJoint;
    //friend class b2PrismaticJoint;
    //friend class b2PulleyJoint;
    //friend class b2RevoluteJoint;
    //friend class b2RopeJoint;
    //friend class b2WeldJoint;
    //friend class b2WheelJoint;
    public const FLAG_ISLAND = 0x1;
    public const FLAG_AWAKE = 0x2;
    public const FLAG_AUTO_SLEEP = 0x4;
    public const FLAG_BULLET = 0x8;
    public const FLAG_FIXED_ROTATION = 0x10;
    public const FLAG_ENABLED = 0x20;
    public const FLAG_TOI = 0x40;

    private World $world;
    private int $flags;

    /** @var int bodydef type */
    private int $type;

    private int $islandIndex;

    private Transform $xf;

    private Sweep $sweep;

    private Vec2 $linearVelocity;
    private float $angularVelocity;

    private Vec2 $force;
    private float $torque;

    private ?array $fixtureList;
    private ?array $jointList;
    private ?array $contactList;

    private float $mass;
    private float $invMass;

    // Rotational inertia about the center of mass.
    private float $I;
    private float $invI;

    private float $linearDamping;
    private float $angularDamping;
    private float $gravityScale;
    private float $sleepTime;

    /** @var mixed|null */
    private $userData;
    public function __construct(BodyDef $bd, World $world)
    {
        Assert::true($bd->position->IsValid());
        Assert::true($bd->linearVelocity->IsValid());
        Assert::true(Math::IsValid($bd->angle));
        Assert::true(Math::IsValid($bd->angularVelocity));
        Assert::true(Math::IsValid($bd->angularDamping) && $bd->angularDamping >= 0.0);
        Assert::true(Math::IsValid($bd->linearDamping) && $bd->linearDamping >= 0.0);

        $this->flags = 0;

        if ($bd->bullet) {
            $this->flags |= self::FLAG_BULLET;
        }
        if ($bd->fixedRotation) {
            $this->flags |= self::FLAG_FIXED_ROTATION;
        }
        if ($bd->allowSleep) {
            $this->flags |= self::FLAG_AUTO_SLEEP;
        }
        if ($bd->awake && $bd->type != BodyDef::TYPE_STATIC_BODY) {
            $this->flags |= self::FLAG_AWAKE;
        }
        if ($bd->enabled) {
            $this->flags |= self::FLAG_ENABLED;
        }

        $this->world = $world;

        $this->xf = new Transform();
        $this->xf->p = $bd->position;
        $this->xf->q->Set($bd->angle);

        $this->sweep = new Sweep();
        $this->sweep->localCenter->SetZero();
        $this->sweep->c0 = $this->xf->p;
        $this->sweep->c = $this->xf->p;
        $this->sweep->a0 = $bd->angle;
        $this->sweep->a = $bd->angle;
        $this->sweep->alpha0 = 0.0;

        $this->jointList = null;
        $this->contactList = null;

        $this->linearVelocity = $bd->linearVelocity;
        $this->angularVelocity = $bd->angularVelocity;

        $this->linearDamping = $bd->linearDamping;
        $this->angularDamping = $bd->angularDamping;
        $this->gravityScale = $bd->gravityScale;

        $this->force = new Vec2();
        $this->force->SetZero();
        $this->torque = 0.0;

        $this->sleepTime = 0.0;

        $this->type = $bd->type;

        $this->mass = 0.0;
        $this->invMass = 0.0;

        $this->I = 0.0;
        $this->invI = 0.0;

        $this->userData = $bd->userData;

        $this->fixtureList = null;
    }

    public function CreateFixture(object $def, float $density = 0): Fixture
    {
        if (!$def instanceof FixtureDef && !$def instanceof Shape) {
            throw new \InvalidArgumentException(sprintf('Expected type %s or %s, %s given.', FixtureDef::class, Shape::class, get_class($def)));
        }
        if ($def instanceof Shape) {
            return $this->CreateFixtureShapeDensity($def, $density);
        }

        return $this->CreateFixtureDef($def);
    }

    private function CreateFixtureDef(FixtureDef $def): Fixture
    {
        Assert::false($this->world->IsLocked(), 'World is locked, impossible to create fixture');
	    $fixture = new Fixture;
	    $fixture->Create($this, $def);

        if ($this->flags & self::FLAG_ENABLED)
        {
            $broadPhase = $this->world->contactManager->broadPhase;
            $fixture->CreateProxies($broadPhase, $this->xf);
        }

        $this->fixtureList[] = $fixture;
        $fixture->body = $this;

        // Adjust mass properties if needed.
        if ($fixture->density > 0.0)
        {
            $this->ResetMassData();
        }

        // Let the world know we have a new fixture. This will cause new contacts
        // to be created at the beginning of the next time step.
        $this->world->newContacts = true;

        return $fixture;
    }

    public function ResetMassData()
    {
        // Compute mass data from shapes. Each shape has its own density.
        $this->mass = 0.0;
        $this->invMass = 0.0;
        $this->I = 0.0;
        $this->invI = 0.0;
        $this->sweep->localCenter->SetZero();

        // Static and kinematic bodies have zero mass.
        if ($this->type == BodyDef::TYPE_STATIC_BODY || $this->type == BodyDef::TYPE_KINEMATIC_BODY)
        {
            $this->sweep->c0 = $this->xf->p;
            $this->sweep->c = $this->xf->p;
            $this->sweep->a0 = $this->sweep->a;
            return;
        }

        Assert::true($this->type == BodyDef::TYPE_DYNAMIC_BODY);

        // Accumulate mass over all fixtures.
        $localCenter = Vec2::zero();
        foreach ($this->fixtureList as $f)
        {
            if ($f->density == 0.0)
            {
                continue;
            }

            $massData = new MassData();
            $f->GetMassData($massData);
            $this->mass += $massData->mass;
            $localCenter->Add($massData->center->Multiply($massData->mass));
            $this->I += $massData->I;
        }

        // Compute center of mass.
        if ($this->mass > 0.0)
        {
            $this->invMass = 1.0 / $this->mass;
            $localCenter->Multiply($this->invMass);
        }

        if ($this->I > 0.0 && ($this->flags & self::FLAG_FIXED_ROTATION) == 0)
        {
            // Center the inertia about the center of mass.
            $this->I -= $this->mass * Math::Dot($localCenter, $localCenter);
            Assert::true($this->I > 0.0);
            $this->invI = 1.0 / $this->I;

        }
        else
        {
            $this->I = 0.0;
            $this->invI = 0.0;
        }

        // Move center of mass.
        $oldCenter = $this->sweep->c;
        $this->sweep->localCenter = $localCenter;
        $this->sweep->c0 = $this->sweep->c = Math::Mul($this->xf, $this->sweep->localCenter);

        // Update center of mass velocity.
        $this->linearVelocity->Add(Math::Cross($this->angularVelocity, $this->sweep->c - $oldCenter));
    }

    public function SetAwake(bool $flag): void
    {
        if ($this->type == BodyDef::TYPE_STATIC_BODY)
        {
            return;
        }

        if ($flag)
        {
            $this->flags |= self::FLAG_AWAKE;
            $this->sleepTime = 0.0;
        }
        else
        {
            $this->flags &= ~self::FLAG_AWAKE;
            $this->sleepTime = 0.0;
            $this->linearVelocity->SetZero();
            $this->angularVelocity = 0.0;
            $this->force->SetZero();
            $this->torque = 0.0;
        }
    }

    private function CreateFixtureShapeDensity(Shape $shape, float $density): Fixture
    {
        throw new \RuntimeException('This method is not defined yet. Please add it or do not use it');
    }
}
