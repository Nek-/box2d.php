<?php

namespace Box2d\src\Common;


use Box2d\Common\Joint;
use Box2d\Common\Timer;
use Box2d\Common\TimeStep;
use Box2d\Common\Math\Vec2;
use Box2d\Common\TimeStep\Profile;
use Box2d\Dynamics\Body;
use Box2d\Dynamics\BodyDef;
use Box2d\Dynamics\ContactManager;
use Box2d\Dynamics\WorldCallbacks\ContactFilterInterface;
use Box2d\Dynamics\WorldCallbacks\ContactListenerInterface;
use Webmozart\Assert\Assert;
use function Box2d\Common\ClearForces;
use function Box2d\Common\Collide;
use function Box2d\Common\GetMilliseconds;
use function Box2d\Common\Solve;
use function Box2d\Common\SolveTOI;
use const Box2d\Common\b2Timer;
use const Box2d\Common\collide;
use const Box2d\Common\dt;
use const Box2d\Common\dtRatio;
use const Box2d\Common\f;
use const Box2d\Common\inv_dt;
use const Box2d\Common\m_clearForces;
use const Box2d\Common\m_contactManager;
use const Box2d\Common\m_continuousPhysics;
use const Box2d\Common\m_inv_dt0;
use const Box2d\Common\m_locked;
use const Box2d\Common\m_profile;
use const Box2d\Common\m_stepComplete;
use const Box2d\Common\m_warmStarting;
use const Box2d\Common\positionIterations;
use const Box2d\Common\solve;
use const Box2d\Common\solveTOI;
use const Box2d\Common\step;
use const Box2d\Common\stepTimer;
use const Box2d\Common\timer;
use const Box2d\Common\velocityIterations;
use const Box2d\Common\warmStarting;

class World
{
    //friend class b2Body;
    //friend class b2Fixture;
    //friend class b2ContactManager;
    //friend class b2Controller;

    public ContactManager $contactManager; // Should be private but is friend with body that uses it and we cant do that in php so here is public.

    /** @var Body[] */
    private array $bodyList;
    /** @var Joint[] */
    private array $jointList;

    private int $bodyCount;
    private int $jointCount;

    private Vec2 $gravity;
    private bool $allowSleep;

//    private ?DestructionListener $destructionListener;
//    private Draw $debugDraw;

    // This is used to compute the time step ratio to
    // support a variable time step.
    private float $inv_dt0;

    /*private*/public bool $newContacts;
    private bool $locked;
    private bool $clearForces;

    // These are for debugging the solver.
    private bool $warmStarting;
    private bool $continuousPhysics;
    private bool $subStepping;

    private bool $stepComplete;

    private Profile $profile;

    public function __construct(Vec2 $gravity)
    {
//        $this->destructionListener = null;
//        $this->debugDraw = null;
        $this->bodyList = [];
        $this->jointList = [];

        $this->warmStarting = true;
        $this->continuousPhysics = true;
        $this->subStepping = false;

        $this->stepComplete = true;

        $this->gravity = $gravity;
        $this->allowSleep = true;

        $this->newContacts = false;
        $this->locked = false;
        $this->clearForces = true;

        $this->inv_dt0 = 0.0;

        $this->contactManager = new ContactManager();
        $this->profile = new Profile();
    }

    public function SetContactFilter(ContactFilterInterface $contactFilter)
    {
        $this->contactManager->contactFilter = $contactFilter;
    }

    public function SetContactListener(ContactListenerInterface $contactListener)
    {
        $this->contactManager->contactListener = $contactListener;
    }

    public function CreateBody(BodyDef $bodyDef): Body
    {
        Assert::false($this->IsLocked(), 'World is locked');

        $b = new Body($bodyDef, $this);

        $this->bodyList[] = $b;

        return $b;
    }

    public function Step(float $dt, int $velocityIterations, int $positionIterations)
    {
        $stepTimer = new Timer();

        // If new fixtures were added, we need to find the new contacts.
        if ($this->newContacts)
        {
            $this->contactManager->FindNewContacts();
            $this->newContacts = false;
        }

        $this->locked = true;

        $step = new TimeStep();
        $step->dt = $dt;
        step.velocityIterations	= velocityIterations;
        step.positionIterations = positionIterations;
        if (dt > 0.0f)
        {
            step.inv_dt = 1.0f / dt;
        }
        else
        {
            step.inv_dt = 0.0f;
        }

        step.dtRatio = m_inv_dt0 * dt;

        step.warmStarting = m_warmStarting;

        // Update contacts. This is where some contacts are destroyed.
        {
            b2Timer timer;
            m_contactManager.Collide();
            m_profile.collide = timer.GetMilliseconds();
        }

        // Integrate velocities, solve velocity constraints, and integrate positions.
        if (m_stepComplete && step.dt > 0.0f)
        {
            b2Timer timer;
            Solve(step);
            m_profile.solve = timer.GetMilliseconds();
        }

        // Handle TOI events.
        if (m_continuousPhysics && step.dt > 0.0f)
        {
            b2Timer timer;
            SolveTOI(step);
            m_profile.solveTOI = timer.GetMilliseconds();
        }

        if (step.dt > 0.0f)
        {
            m_inv_dt0 = step.inv_dt;
        }

        if (m_clearForces)
        {
            ClearForces();
        }

        m_locked = false;

        m_profile.step = stepTimer.GetMilliseconds();
    }

    public function IsLocked(): bool
    {
        return $this->locked;
    }
}
