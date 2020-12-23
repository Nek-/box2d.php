<?php

namespace Box2d\Common;

use Box2d\Settings as GlobalSettings;

class Settings
{
    public const linearSlop = 0.005 * GlobalSettings::lengthUnitsPerMeter;

    /// This is used to fatten AABBs in the dynamic tree. This allows proxies
    /// to move by a small amount without triggering a tree adjustment.
    /// This is in meters.
    public const aabbExtension = 0.1 * GlobalSettings::lengthUnitsPerMeter;
}
