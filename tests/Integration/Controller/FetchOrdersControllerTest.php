<?php

declare(strict_types=1);

namespace App\Tests\Integration\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class FetchOrdersControllerTest extends WebTestCase
{
    public function testInvokeDispatchesMessage(): void
    {
        $client = static::createClient();

        // Mock the use case to verify execution and prevent actual logic
        $useCase = $this->createMock(\App\UseCase\FetchOrdersUseCaseInterface::class);
        $useCase->expects($this->once())
            ->method('execute')
            ->with($this->callback(function (\App\DTO\FetchOrdersRequest $request) {
                return $request->date_from === strtotime('2023-01-01')
                    && 'allegro' === $request->filter_order_source;
            }));

        static::getContainer()->set(\App\UseCase\FetchOrdersUseCaseInterface::class, $useCase);

        $timestamp = strtotime('2023-01-01');
        $client->jsonRequest('POST', '/api/orders/fetch', [
            'date_from' => $timestamp,
            'filter_order_source' => 'allegro',
        ]);

        $this->assertResponseStatusCodeSame(202);

        $content = (string) $client->getResponse()->getContent();
        $this->assertJson($content);
        $decoded = json_decode($content, true);
        $this->assertArrayHasKey('status', $decoded);
        $this->assertEquals('Order fetch job dispatched', $decoded['status']);
    }

    public function testInvokeHandlesInvalidDate(): void
    {
        $client = static::createClient();

        // UseCase should NOT be called
        $useCase = $this->createMock(\App\UseCase\FetchOrdersUseCaseInterface::class);
        $useCase->expects($this->never())->method('execute');
        static::getContainer()->set(\App\UseCase\FetchOrdersUseCaseInterface::class, $useCase);

        $client->jsonRequest('POST', '/api/orders/fetch', [
            'date_from' => 'invalid-date',
        ]);

        $this->assertResponseStatusCodeSame(422);
    }
}
