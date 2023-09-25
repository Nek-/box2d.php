<?php

namespace Box2d\Common;

use Box2d\Settings as GlobalSettings;

class Common
{
    public const pi = 3.14159265359;
    public const maxFloat = PHP_FLOAT_MAX;
    public const epsilon = PHP_FLOAT_EPSILON;

    /// @file
    /// Global tuning constants based on meters-kilograms-seconds (MKS) units.
    ///


    // Collision

    /// The maximum number of contact points between two convex shapes. Do
    /// not change this value.
    public const maxManifoldPoints = 2;
    /// This is used to fatten AABBs in the dynamic tree. This allows proxies
    /// to move by a small amount without triggering a tree adjustment.
    /// This is in meters.
    public const aabbExtension = 0.1 * GlobalSettings::lengthUnitsPerMeter;

    public const linearSlop = 0.005 * GlobalSettings::lengthUnitsPerMeter;

    /// This is used to fatten AABBs in the dynamic tree. This is used to predict
    /// the future position based on the current displacement.
    /// This is a dimensionless multiplier.
    public const aabbMultiplier = 4.0;

    /// A small angle used as a collision and constraint tolerance. Usually it is
    /// chosen to be numerically significant, but visually insignificant.
    public const angularSlop = 2.0 / 180.0 * self::pi;

    /// The radius of the polygon/edge shape skin. This should not be modified. Making
    /// this smaller means polygons will have an insufficient buffer for continuous collision.
    /// Making it larger may create artifacts for vertex collision.
    public const polygonRadius = 2.0 * self::linearSlop;

    /// Maximum number of sub-steps per contact in continuous physics simulation.
    public const maxSubSteps = 8;

    // Dynamics

    /// Maximum number of contacts to be handled to solve a TOI impact.
    public const maxTOIContacts = 32;

    /// The maximum linear position correction used when solving constraints. This helps to
    /// prevent overshoot. Meters.
    public const maxLinearCorrection = 0.2 * GlobalSettings::lengthUnitsPerMeter;

    /// The maximum angular position correction used when solving constraints. This helps to
    /// prevent overshoot.
    public const maxAngularCorrection = 8.0 / 180 * self::pi;

    /// The maximum linear translation of a body per step. This limit is very large and is used
    /// to prevent numerical problems. You shouldn't need to adjust this. Meters.
    public const maxTranslation = 2.0 * GlobalSettings::lengthUnitsPerMeter;
    public const maxTranslationSquared = self::maxTranslation * self::maxRotation;

    /// The maximum angular velocity of a body. This limit is very large and is used
    /// to prevent numerical problems. You shouldn't need to adjust this.
    public const maxRotation = 0.5 * self::pi;
    public const maxRotationSquared = self::maxRotation * self::maxRotation;

    /// This scale factor controls how fast overlap is resolved. Ideally this would be 1 so
    /// that overlap is removed in one time step. However using values close to 1 often lead
    /// to overshoot.
    public const baumgarte = 0.2;
    public const toiBaumgarte = 0.75;

    // Sleep

    /// The time that a body must be still before it will go to sleep.
    public const timeToSleep = 0.5;

    /// A body cannot sleep if its linear velocity is above this tolerance.
    public const linearSleepTolerance = 0.01 * GlobalSettings::lengthUnitsPerMeter;

    /// A body cannot sleep if its angular velocity is above this tolerance.
    public const angularSleepTolerance = 2.0 / 180.0 * self::pi;
}
