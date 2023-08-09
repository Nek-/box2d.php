<?php

namespace Box2d\Dynamics;


use Box2d\Dynamics\Contact;
use Box2d\Collision\BroadPhase\BroadPhase;
use Box2d\Dynamics\WorldCallbacks\ContactFilter;
use Box2d\Dynamics\WorldCallbacks\ContactFilterInterface;
use Box2d\Dynamics\WorldCallbacks\ContactListener;
use Box2d\Dynamics\WorldCallbacks\ContactListenerInterface;

class ContactManager
{

    /*private*/public BroadPhase $broadPhase;
    /** @var Contact[] */
    private $contactList;
    /*private*/public ContactFilterInterface $contactFilter;
    /*private*/public ContactListenerInterface $contactListener;

    public function __construct()
    {
        $this->contactList = [];
        $this->contactFilter = new ContactFilter();
        $this->contactListener = new ContactListener();
    }
}
