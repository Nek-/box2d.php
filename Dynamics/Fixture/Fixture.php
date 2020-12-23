<?php

namespace Box2d\Dynamics\Fixture;


use Box2d\Collision\BroadPhase\BroadPhase;
use Box2d\Collision\Shape\MassData;
use Box2d\Collision\Shape\Shape;
use Box2d\Common\Math\Transform;
use Box2d\Dynamics\Body;
use Webmozart\Assert\Assert;

class Fixture
{
    /*protected*/public ?Body $body;

    /*protected*/public float $density;

//    protected Fixture $next;

    protected ?Shape $shape;
    protected float $friction;
    protected float $restitution;
    protected float $restitutionThreshold;
    /** @var ?FixtureProxy[] */
    protected ?array $proxies;
    protected Filter $filter;

    protected bool $isSensor;

    /** @var mixed|null */
    protected $userData;

    public function __construct()
    {
        $this->body = null;
        $this->proxies = null;
        $this->shape = null;
        $this->density = 0;
    }

    public function Create(Body $body, FixtureDef $def)
    {
        $this->userData = $def->userData;
	    $this->friction = $def->friction;
	    $this->restitution = $def->restitution;
        $this->restitutionThreshold = $def->restitutionThreshold;

        $this->body = $body;

        $this->filter = $def->filter;

        $this->isSensor = $def->isSensor;

        $this->shape = $def->shape === null ? null : clone $def->shape;

        // Reserve proxy space
        $childCount = $this->shape->GetChildCount();
        $this->proxies = [];
        for ($i = 0; $i < $childCount; ++$i)
        {
            $this->proxies[$i] = new FixtureProxy;
            $this->proxies[$i]->fixture = null;
            $this->proxies[$i]->proxyId = BroadPhase::NULL_PROXY;
        }

        $this->density = $def->density;
    }

    public function CreateProxies(BroadPhase $broadPhase, Transform $xf)
    {
        Assert::count($this->proxies, 0, 'Proxies already created');

        // Create proxies in the broad-phase.
        $proxyCount = $this->shape->GetChildCount();

        for ($i = 0; $i < $proxyCount; ++$i)
        {
            $proxy = new FixtureProxy();
            $this->shape->ComputeAABB($proxy->aabb, $xf, $i);
            $proxy->proxyId = $broadPhase->CreateProxy($proxy->aabb, $proxy);
            $proxy->fixture = $this;
            $proxy->childIndex = $i;
            $this->proxies[] = $proxy;
        }
    }

    public function GetMassData(MassData $massData)
    {
        $this->shape->ComputeMass($massData, $this->density);
    }

    public function SetSensor(bool $sensor): void
    {
        if ($sensor != $this->isSensor)
        {
            $this->body->SetAwake(true);
		    $this->isSensor = $sensor;
	    }
    }

    public function GetFilterData()
    {

    }


}
