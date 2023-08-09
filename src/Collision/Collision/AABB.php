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
