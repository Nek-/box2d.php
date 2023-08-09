<?php

namespace Box2d\Common\Math;


class Math
{
    public static function IsValid(float $x): bool
    {
        return \is_finite($x);
    }

    /**
     * @param float|int $a
     * @param float|int $b
     * @return float|int
     */
    public static function Min($a, $b)
    {
        return $a < $b ? $a : $b;
    }

    /**
     * @param float|int $a
     * @param float|int $b
     * @return float|int
     */
    public static function Max($a, $b)
    {
        return $a > $b ? $a : $b;
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

    /**
     * @param Vec2|float $a
     * @param Vec2 $b
     *
     * @return float|Vec2
     * @throws \Exception
     */
    public static function Cross($a, $b)
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
    private static function CrossFloatWithVec2(float $s, Vec2 $a)
    {
        return new Vec2(-$s * $a->y, $s * $a->x);
    }
}
