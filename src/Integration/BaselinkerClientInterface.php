<?php

declare(strict_types=1);

namespace App\Integration;

interface BaselinkerClientInterface
{
    /** @return array<string, mixed> */
    public function getOrders(\DateTimeInterface $from): array;

    /**
     * @param array<string, mixed> $params
     *
     * @return array<string, mixed>
     */
    public function getOrderDetails(array $params): array;
}
