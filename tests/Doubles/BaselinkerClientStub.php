<?php

declare(strict_types=1);

namespace App\Tests\Doubles;

use App\Integration\BaselinkerClientInterface;

final class BaselinkerClientStub implements BaselinkerClientInterface
{
    /** @var array<string, mixed> */
    private array $ordersResponse;

    /** @var array<string, mixed> */
    private array $orderSourcesResponse;

    /** @var array<string, mixed> */
    private array $orderStatusListResponse;

    /** @var array<string, mixed> */
    private array $orderTransactionDataResponse;

    /** @var array<string, mixed>|null */
    public ?array $lastFilters = null;

    public ?int $lastOrderIdForTransaction = null;

    /**
     * @param array<string, mixed> $ordersResponse
     * @param array<string, mixed> $orderSourcesResponse
     * @param array<string, mixed> $orderStatusListResponse
     * @param array<string, mixed> $orderTransactionDataResponse
     */
    public function __construct(
        array $ordersResponse = [],
        array $orderSourcesResponse = [],
        array $orderStatusListResponse = [],
        array $orderTransactionDataResponse = [],
    ) {
        $this->ordersResponse = $ordersResponse;
        $this->orderSourcesResponse = $orderSourcesResponse;
        $this->orderStatusListResponse = $orderStatusListResponse;
        $this->orderTransactionDataResponse = $orderTransactionDataResponse;
    }

    public function getOrders(\DateTimeInterface $from, array $filters = []): array
    {
        $this->lastFilters = $filters;

        return $this->ordersResponse;
    }

    public function getOrderSources(): array
    {
        return $this->orderSourcesResponse;
    }

    public function getOrderStatusList(): array
    {
        return $this->orderStatusListResponse;
    }

    public function getOrderTransactionData(int $orderId): array
    {
        $this->lastOrderIdForTransaction = $orderId;

        return $this->orderTransactionDataResponse;
    }
}
