<?php

namespace Box2d\Common\TimeStep;

/// Solver Data

class SolverData
{
    public TimeStep $step;
    /** @var Position[] */
    public array $positions;
    /** Velocity[] */
    public array $velocities;
}
