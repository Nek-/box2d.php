<?php

namespace Box2d\Common\TimeStep;

/// Profiling data. Times are in milliseconds.
class Profile
{
    public float $step;
    public float $collide;
    public float $solve;
    public float $solveInit;
    public float $solveVelocity;
    public float $solvePosition;
    public float $broadphase;
    public float $solveTOI;
}
