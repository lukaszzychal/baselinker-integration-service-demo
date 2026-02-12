<?php

declare(strict_types=1);

namespace App\Tests\Unit\Controller;

use App\Controller\GetOrderTransactionController;
use App\Tests\Doubles\BaselinkerClientStub;
use PHPUnit\Framework\TestCase;

class GetOrderTransactionControllerTest extends TestCase
{
    public function testInvokeReturnsTransactionDataFromClient(): void
    {
        $transaction = [
            'status' => 'SUCCESS',
            'order_id' => 143477867,
            'currency' => 'PLN',
            'payment_done' => 1,
        ];

        $client = new BaselinkerClientStub(
            orderTransactionDataResponse: $transaction,
        );

        $controller = new GetOrderTransactionController($client);

        $response = $controller->__invoke(143477867);

        $this->assertSame(200, $response->getStatusCode());

        /** @var array<string, mixed> $data */
        $data = json_decode((string) $response->getContent(), true);

        $this->assertSame('SUCCESS', $data['status']);
        $this->assertSame(143477867, $data['order_id']);
        $this->assertSame('PLN', $data['currency']);

        $this->assertSame(143477867, $client->lastOrderIdForTransaction);
    }

    public function testInvokeCallsClientWithCorrectOrderId(): void
    {
        $client = new BaselinkerClientStub(
            orderTransactionDataResponse: ['status' => 'SUCCESS'],
        );

        $controller = new GetOrderTransactionController($client);

        $controller->__invoke(999);

        $this->assertSame(999, $client->lastOrderIdForTransaction);
    }
}
