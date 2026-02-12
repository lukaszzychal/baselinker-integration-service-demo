<?php

declare(strict_types=1);

namespace App\Factory;

use App\DTO\FetchOrdersRequest;
use App\Message\FetchOrders;

final readonly class FetchOrdersMessageFactory
{
    public function createFromRequest(FetchOrdersRequest $request): FetchOrders
    {
        return new FetchOrders(
            from: $this->resolveStartDate($request),
            marketplace: $request->filter_order_source ?? 'all',
            filters: $this->resolveFilters($request)
        );
    }

    private function resolveStartDate(FetchOrdersRequest $request): \DateTimeImmutable
    {
        $timestamp = $request->date_from ?? $request->date_confirmed_from ?? time() - 86400;

        return (new \DateTimeImmutable())->setTimestamp((int) $timestamp);
    }

    /**
     * @return array<string, mixed>
     */
    private function resolveFilters(FetchOrdersRequest $request): array
    {
        $filters = [
            'order_id' => $request->order_id,
            'id_from' => $request->id_from,
            'get_unconfirmed_orders' => $request->get_unconfirmed_orders,
            'status_id' => $request->status_id,
            'filter_email' => $request->filter_email,
            'filter_order_source_id' => $request->filter_order_source_id,
            'filter_shop_order_id' => $request->filter_shop_order_id,
            'include_custom_extra_fields' => $request->include_custom_extra_fields,
            'include_commission_data' => $request->include_commission_data,
            'include_connect_data' => $request->include_connect_data,
        ];

        return $this->filterEmptyValues($filters);
    }

    /**
     * @param array<string, mixed> $filters
     *
     * @return array<string, mixed>
     */
    private function filterEmptyValues(array $filters): array
    {
        return array_filter($filters, fn ($v) => null !== $v && false !== $v);
    }
}
