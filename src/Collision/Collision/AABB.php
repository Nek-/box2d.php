<?php

namespace Box2d\Collision\Collision;


use Box2d\Common\Math\Vec2;

class AABB
{
    public Vec2 $lowerBound; ///< the lower vertex
    public Vec2 $upperBound; ///< the upper vertex

    /// Get the perimeter length
    public function GetPerimeter(): float
	{
		$wx = $this->upperBound->x - $this->lowerBound->x;
		$wy = $this->upperBound->y - $this->lowerBound->y;

		return 2.0 * ($wx + $wy);
	}

	public function Combine(AABB $aabb, AABB $aabb2 = null): void
	{
	    if ($aabb2 === null) {
	        $this->simpleCombine($aabb);
	        return;
        }

	    $this->doubleCombine($aabb, $aabb2);
	}

    public function GetCenter(): Vec2
    {
        return $this->lowerBound->Add($this->upperBound)->Multiply(0.5);
    }

    public function GetExtents(): Vec2
    {
        return $this->upperBound->Subtract($this->lowerBound)->Multiply(0.5);
    }

    public function IsValid(): bool
    {
        $d = $this->upperBound->Subtract($this->lowerBound);
        $valid = $d->x >= 0.0 && $d->y >= 0.0;

        return $valid && $this->lowerBound->IsValid() && $this->upperBound->IsValid();
    }

    public function TestOverlap(AABB $a, AABB $b):bool
    {
        $d1 = $a->lowerBound->Subtract($a->upperBound);
        $d2 = $b->lowerBound->Subtract($b->upperBound);

        if ($d1->x > 0.0 || $d1->y > 0.0) {
            return false;
        }

	    if ($d2->x > 0.0 || $d2->y > 0.0) {
            return false;
        }

	    return true;
    }

    public function Contains(AABB $aabb): bool
    {
        $result = true;

        $result = $result && $this->lowerBound->x <= $aabb->lowerBound->x;
        $result = $result && $this->lowerBound->y <= $aabb->lowerBound->y;
        $result = $result && $aabb->upperBound->x <= $this->upperBound->x;
        $result = $result && $aabb->upperBound->y <= $this->upperBound->y;

        return $result;
    }

    /// Combine an AABB into this one.
	private function simpleCombine(AABB $aabb)
    {
        $this->lowerBound = Vec2::Min($this->lowerBound, $aabb->lowerBound);
        $this->upperBound = Vec2::Max($this->upperBound, $aabb->upperBound);
    }
    /// Combine two AABBs into this one.
    private function doubleCombine(AABB $aabb1, AABB $aabb2)
    {
        $this->lowerBound = Vec2::Min($aabb1->lowerBound, $aabb2->lowerBound);
        $this->upperBound = Vec2::Max($aabb1->upperBound, $aabb2->upperBound);
    }
}
