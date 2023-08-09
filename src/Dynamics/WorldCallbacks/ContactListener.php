<?php

namespace Box2d\Dynamics\WorldCallbacks;


use Box2d\Dynamics\WorldCallbacks\ContactImpulse;
use Box2d\Dynamics\WorldCallbacks\Manifold;
use Box2d\Dynamics\Contact\Contact;

class ContactListener implements ContactListenerInterface
{
    public function BeginContact(Contact $contact): void {}

    public function EndContact(Contact $contact): void {}

    public function PreSolve(Contact $contact, Manifold $oldManifold): void {}

    public function PostSolve(Contact $contact, ContactImpulse $impulse): void {}
}
