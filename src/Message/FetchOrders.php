<?php

declare(strict_types=1);

namespace App\Message;

final readonly class FetchOrders
{
    public function __construct(
        /**
         * Maps to 'date_confirmed_from' (Unix timestamp) in Baselinker API.
         */
        public \DateTimeInterface $from,

        /**
         * Maps to 'filter_order_source' in Baselinker API.
         */
        public string $marketplace = 'all',

        /**
         * Additional filters mapping to parameters like 'status_id', 'filter_email', etc.
         */
        public array $filters = [],
    ) {
    }
}
