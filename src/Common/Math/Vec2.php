<?php

namespace Box2d\Common\Math;

use Box2d\Common\Common;
use function Box2d\Common\IsValid;

class Vec2
{
    public float $x;
    public float $y;
    public function __construct(float $x = 0, float $y = 0)
    {
        $this->x = $x;
        $this->y = $y;
    }

    public function Set(float $x, float $y)
    {
        $this->x = $x;
        $this->y = $y;
    }

    public static function DistanceSquared(Vec2 $a, Vec2 $b): float
    {
        $c_x = $a->x - $b->x;
        $c_y = $a->y - $b->y;

        return sqrt($c_x * $c_x + $c_y * $c_y);
    }

    public static function Min(Vec2 $a, Vec2 $b)
    {
        return new Vec2(Math::Min($a->x, $b->x), Math::Min($a->y, $b->y));
    }

    public static function Max(Vec2 $a, Vec2 $b)
    {
        return new Vec2(Math::Max($a->x, $b->x), Math::Max($a->y, $b->y));
    }

    public static function zero(): Vec2
    {
        return new Vec2();
    }

    public function Multiply(float $a): self
    {
        $this->x *= $a;
        $this->y *= $a;

        return $this;
    }

    public function Add(Vec2 $v): self
    {
        $this->x += $v->x;
        $this->y += $v->y;

        return $this;
    }

    public function Subtract(Vec2 $v): self
    {
        $this->x -= $v->x;
        $this->y -= $v->y;

        return $this;
    }

    public function SetZero()
    {
        $this->x = 0.0;
        $this->y = 0.0;
    }

    /// Get the length squared. For performance, use this instead of
    /// b2Vec2::Length (if possible).
    public function LengthSquared(): float
	{
		return $this->x * $this->x + $this->y * $this->y;
	}

    /// Get the length of this vector (the norm).
    public function Length(): float
    {
        return sqrt($this->x * $this->x + $this->y * $this->y);
    }

    public function Normalize(): float
    {
        $length = $this->Length();

        if ($length < Common::epsilon) {
            return 0.0;
        }

        $invLength = 1.0 / $length;
        $this->x *= $invLength;
        $this->y *= $invLength;

        return $length;
    }

    /// Does this vector contain finite coordinates?
    public function IsValid(): bool
	{
		return IsValid($this->x) && IsValid($this->y);
	}

    /// Get the skew vector such that dot(skew_vec, other) == cross(vec, other)
    public function Skew(): Vec2
    {
        return new Vec2(-$this->y, $this->x);
    }

    public function Negate(): Vec2
    {
        return new Vec2(-$this->x, -$this->y);
    }
}
