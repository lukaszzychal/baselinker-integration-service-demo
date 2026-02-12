<?php

declare(strict_types=1);

namespace App\DTO;

use Symfony\Component\Validator\Constraints as Assert;

final readonly class FetchOrdersRequest
{
    public function __construct(
        #[Assert\Type('integer')]
        #[Assert\Positive]
        public ?int $order_id = null,

        #[Assert\Type('integer')]
        #[Assert\PositiveOrZero]
        public ?int $date_confirmed_from = null,

        #[Assert\Type('integer')]
        #[Assert\PositiveOrZero]
        public ?int $date_from = null,

        #[Assert\Type('integer')]
        #[Assert\PositiveOrZero]
        public ?int $id_from = null,

        #[Assert\Type('bool')]
        public bool $get_unconfirmed_orders = false,

        #[Assert\Type('integer')]
        public ?int $status_id = null,

        #[Assert\Email]
        #[Assert\Length(max: 50)]
        public ?string $filter_email = null,

        #[Assert\Type('string')]
        #[Assert\Length(max: 20)]
        public ?string $filter_order_source = null,

        #[Assert\Type('integer')]
        public ?int $filter_order_source_id = null,

        #[Assert\Type('integer')]
        public ?int $filter_shop_order_id = null,

        #[Assert\Type('bool')]
        public bool $include_custom_extra_fields = false,

        #[Assert\Type('bool')]
        public bool $include_commission_data = false,

        #[Assert\Type('bool')]
        public bool $include_connect_data = false,
    ) {
    }

    public function withFilterOrderSource(?string $source): self
    {
        return new self(
            order_id: $this->order_id,
            date_confirmed_from: $this->date_confirmed_from,
            date_from: $this->date_from,
            id_from: $this->id_from,
            get_unconfirmed_orders: $this->get_unconfirmed_orders,
            status_id: $this->status_id,
            filter_email: $this->filter_email,
            filter_order_source: $source,
            filter_order_source_id: $this->filter_order_source_id,
            filter_shop_order_id: $this->filter_shop_order_id,
            include_custom_extra_fields: $this->include_custom_extra_fields,
            include_commission_data: $this->include_commission_data,
            include_connect_data: $this->include_connect_data,
        );
    }
}
