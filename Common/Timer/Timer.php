<?php

namespace Box2d\Common\Timer;


class Timer
{
    private static float $invFrequency = 0.0;
    private \GMP $start_sec;
    private \GMP $start_usec;

    public function __construct()
    {

        $largeInteger;

        if (self::$invFrequency == 0.0)
        {
            QueryPerformanceFrequency(&largeInteger);
            self::$invFrequency = double(largeInteger.QuadPart);
            if (self::$invFrequency > 0.0)
            {
                self::$invFrequency = 1000.0 / self::$invFrequency;
            }
        }

        QueryPerformanceCounter(&largeInteger);
        $this->start = double(largeInteger.QuadPart);
    }
}
