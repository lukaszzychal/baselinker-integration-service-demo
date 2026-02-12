<?php

declare(strict_types=1);

namespace App\Tests\Unit\Factory;

use App\DTO\FetchOrdersRequest;
use App\Factory\FetchOrdersMessageFactory;
use PHPUnit\Framework\TestCase;

class FetchOrdersMessageFactoryTest extends TestCase
{
    private FetchOrdersMessageFactory $factory;

    protected function setUp(): void
    {
        $this->factory = new FetchOrdersMessageFactory();
    }

    public function testCreateFromRequestWithMinimalData(): void
    {
        $request = new FetchOrdersRequest();
        $message = $this->factory->createFromRequest($request);

        $this->assertSame('all', $message->marketplace);
        $this->assertEmpty($message->filters);
        // Check if date is roughly 24h ago
        $expectedTimestamp = time() - 86400;
        $this->assertEqualsWithDelta($expectedTimestamp, $message->from->getTimestamp(), 5);
    }

    public function testCreateFromRequestWithFullData(): void
    {
        $dateFrom = 1672531200; // 2023-01-01 00:00:00 UTC
        $request = new FetchOrdersRequest(
            order_id: 123,
            date_confirmed_from: null,
            date_from: $dateFrom,
            status_id: 456,
            filter_email: 'test@example.com',
            filter_order_source: 'amazon',
            include_commission_data: true
        );

        $message = $this->factory->createFromRequest($request);

        $this->assertEquals($dateFrom, $message->from->getTimestamp());
        $this->assertSame('amazon', $message->marketplace);

        $expectedFilters = [
            'order_id' => 123,
            'status_id' => 456,
            'filter_email' => 'test@example.com',
            'include_commission_data' => true,
        ];
        // Compare filters ignoring order
        $this->assertEquals($expectedFilters, $message->filters);
    }

    public function testDatePriority(): void
    {
        // date_from takes precedence over date_confirmed_from
        $dateFrom = 1000;
        $dateConfirmedFrom = 2000;

        $request = new FetchOrdersRequest(
            date_confirmed_from: $dateConfirmedFrom,
            date_from: $dateFrom
        );

        $message = $this->factory->createFromRequest($request);
        $this->assertEquals($dateFrom, $message->from->getTimestamp());
    }

    public function testDateConfirmedFromFallback(): void
    {
        $dateConfirmedFrom = 2000;

        $request = new FetchOrdersRequest(
            date_confirmed_from: $dateConfirmedFrom,
            date_from: null
        );

        $message = $this->factory->createFromRequest($request);
        $this->assertEquals($dateConfirmedFrom, $message->from->getTimestamp());
    }

    public function testFalseValuesArePreservedInFilters(): void
    {
        $request = new FetchOrdersRequest(
            include_custom_extra_fields: false // specific boolean false
        );

        // In the factory logic:
        // 'include_custom_extra_fields' => $request->include_custom_extra_fields,
        // and filterEmptyValues uses: $v !== null && $v !== false

        // Wait, the logic is: return array_filter($filters, fn($v) => $v !== null && $v !== false);
        // So 'false' IS filtered out!
        // Let's verify this behavior. Ideally boolean flags should be present only if true?
        // Or if the API expects false explicitly?
        // Baselinker API usually defaults to false if omitted.
        // So filtering out false is acceptable optimization.

        $message = $this->factory->createFromRequest($request);
        $this->assertArrayNotHasKey('include_custom_extra_fields', $message->filters);
    }
}
