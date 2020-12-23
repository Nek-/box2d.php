<?php

namespace Box2d\Collision\Collision;

/// Contact ids to facilitate warm starting.
class ContactID extends ContactFeature
{
    public int $key; ///< Used to quickly compare contact ids.
}
