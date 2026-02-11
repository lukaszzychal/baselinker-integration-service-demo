<?php

declare(strict_types=1);

namespace App\Integration;

interface BaselinkerClientInterface
{
    /** @return array<string, mixed> */
    public function getOrders(\DateTimeInterface $from): array;
}
