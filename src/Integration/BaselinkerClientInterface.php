<?php

declare(strict_types=1);

namespace App\Integration;

interface BaselinkerClientInterface
{
    /**
     * @param array<string, mixed> $filters
     *
     * @return array<string, mixed>
     */
    public function getOrders(\DateTimeInterface $from, array $filters = []): array;

    /**
     * Returns order sources grouped by type (personal, shop, marketplace).
     *
     * @return array<string, mixed>
     */
    public function getOrderSources(): array;

    /**
     * Returns order statuses defined by the user.
     *
     * @return array<string, mixed>
     */
    public function getOrderStatusList(): array;
}
