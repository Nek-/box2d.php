<?php

namespace Box2d\Common\Math;


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

    public function Mult(float $a): self
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

    public function Sub(Vec2 $v): self
    {
        $this->x -= $v->x;
        $this->y -= $v->y;

        return $this;
    }


    public function IsValid()
    {
        return \is_finite($this->x) && \is_finite($this->y);
    }

    public function SetZero()
    {
        $this->x = 0.0;
        $this->y = 0.0;
    }
}
