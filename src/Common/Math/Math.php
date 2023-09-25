<?php

namespace Box2d\Common\Math;


class Math
{
    public static function IsValid(float $x): bool
    {
        return \is_finite($x);
    }

    public static function Min(float|int|Vec2 $a, float|int|Vec2 $b): float|int|Vec2
    {
        if (!$a instanceof Vec2 && !$b instanceof Vec2) {
            return $a < $b ? $a : $b;
        }

        if ($a instanceof Vec2 && $b instanceof Vec2) {
            return new Vec2(Math::Min($a->x, $b->x), Math::Min($a->y, $b->y));
        }

        throw new \Exception('unsupported yet, code to write');
    }

    public static function Max(float|int|Vec2 $a, float|int|Vec2 $b): float|int|Vec2
    {
        if (!$a instanceof Vec2 && !$b instanceof Vec2) {
            return $a > $b ? $a : $b;
        }

        if ($a instanceof Vec2 && $b instanceof Vec2) {
            return new Vec2(Math::Max($a->x, $b->x), Math::Max($a->y, $b->y));
        }

        throw new \Exception('unsupported yet, code to write');
    }


    public static function Dot(Vec2 $a, Vec2 $b): float
    {
        return $a->x * $b->x + $a->y * $b->y;
    }

    /**
     * @param Transform $a
     * @param Vec2 $b
     */
    public static function Mul($a, $b): Vec2
    {
        if ($a instanceof Transform && $b instanceof Vec2) {
            return self::MulTransformVec($a, $b);
        }

        throw new \Exception('unsupported yet, code to write');
    }

    private static function MulTransformVec(Transform $T, Vec2 $v)
    {
        $x = ($T->q->c * $v->x - $T->q->s * $v->y) + $T->p->x;
	    $y = ($T->q->s * $v->x + $T->q->c * $v->y) + $T->p->y;

	    return new Vec2($x, $y);
    }

    public static function Abs(int|float|Vec2 $a)
    {
        if (!$a instanceof Vec2) {
            return abs($a);
        }

        return new Vec2(abs($a->x), abs($a->y));
    }

    public static function Cross(Vec2|float $a, Vec2 $b): float|Vec2
    {
        if ($a instanceof Vec2) {
            if ($b instanceof Vec2) {
                return self::CrossVec2($a, $b);
            }
        }
        if (is_float($a) && $b instanceof Vec2) {
            return self::CrossFloatWithVec2($a, $b);
        }

        throw new \Exception('unsupported yet, code to write');
    }

    /// Perform the cross product on two vectors. In 2D this produces a scalar.
    private static function CrossVec2(Vec2 $a, Vec2 $b): float
    {
        return $a->x * $b->y - $a->y * $b->x;
    }
    private static function CrossFloatWithVec2(float $s, Vec2 $a): Vec2
    {
        return new Vec2(-$s * $a->y, $s * $a->x);
    }
}
