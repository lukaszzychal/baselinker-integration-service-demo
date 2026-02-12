<?php

declare(strict_types=1);

namespace App\Integration;

use Ackintosh\Ganesha;
use Ackintosh\Ganesha\Storage\Adapter\Apcu;

/**
 * Builds Ganesha Circuit Breaker instance with APCu adapter (no Redis required).
 */
final class GaneshaFactory
{
    private const TIME_WINDOW = 30;
    private const FAILURE_RATE_THRESHOLD = 50;
    private const MINIMUM_REQUESTS = 10;
    private const INTERVAL_TO_HALF_OPEN = 10;

    public static function create(): Ganesha
    {
        $adapter = new Apcu();

        return Ganesha\Builder::withRateStrategy()
            ->adapter($adapter)
            ->timeWindow(self::TIME_WINDOW)
            ->failureRateThreshold(self::FAILURE_RATE_THRESHOLD)
            ->minimumRequests(self::MINIMUM_REQUESTS)
            ->intervalToHalfOpen(self::INTERVAL_TO_HALF_OPEN)
            ->build();
    }
}
