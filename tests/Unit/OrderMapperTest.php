<?php

declare(strict_types=1);

namespace App\Tests\Unit;

use App\Integration\OrderMapper;
use PHPUnit\Framework\TestCase;

final class OrderMapperTest extends TestCase
{
    private OrderMapper $mapper;

    protected function setUp(): void
    {
        $this->mapper = new OrderMapper();
    }

    public function testMapsRawOrderToDTO(): void
    {
        $raw = [
            'order_id' => '123',
            'order_source' => 'allegro',
            'user_login' => 'john.doe',
            'date_add' => 1234567890,
            'delivery_price' => 10.0,
            'products' => [
                ['price_brutto' => 50.0, 'quantity' => 2],
                ['price_brutto' => 20.0, 'quantity' => 1],
            ],
        ];

        $dto = $this->mapper->map($raw);

        $this->assertEquals('123', $dto->externalId);
        $this->assertEquals('allegro', $dto->marketplace);
        $this->assertEquals('john.doe', $dto->customerName);
        $this->assertEquals(13000, $dto->totalAmount);
        $this->assertEquals(1234567890, $dto->createdAt->getTimestamp());
    }

    public function testHandlesMissingFieldsGracefully(): void
    {
        $raw = [
            'order_id' => '999',
            // missing order_source
            // missing user_login
            'date_add' => time(),
            // missing delivery_price
            // missing products
        ];

        $dto = $this->mapper->map($raw);

        $this->assertEquals('999', $dto->externalId);
        $this->assertEquals('unknown', $dto->marketplace);
        $this->assertEquals('', $dto->customerName);
        $this->assertEquals(0, $dto->totalAmount);
        $this->assertEmpty($dto->products);
    }
}
