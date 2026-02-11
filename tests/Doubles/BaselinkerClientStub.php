<?php

declare(strict_types=1);

namespace App\Tests\Doubles;

use App\Integration\BaselinkerClientInterface;

final class BaselinkerClientStub implements BaselinkerClientInterface
{
    /** @var array<string, mixed> */
    private array $ordersResponse;

    /**
     * @param array<string, mixed> $ordersResponse
     */
    public function __construct(array $ordersResponse = [])
    {
        $this->ordersResponse = $ordersResponse;
    }

    public function getOrders(\DateTimeInterface $from): array
    {
        return $this->ordersResponse;
    }

    public function getOrderDetails(array $params): array
    {
        return []; // Not needed for current tests
    }
}
