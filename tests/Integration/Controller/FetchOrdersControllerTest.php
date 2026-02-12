<?php

declare(strict_types=1);

namespace App\Tests\Integration\Controller;

use App\Tests\Doubles\FetchOrdersUseCaseSpy;
use App\UseCase\FetchOrdersUseCaseInterface;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class FetchOrdersControllerTest extends WebTestCase
{
    public function testInvokeDispatchesMessage(): void
    {
        $client = static::createClient();

        $useCaseSpy = new FetchOrdersUseCaseSpy();
        static::getContainer()->set(FetchOrdersUseCaseInterface::class, $useCaseSpy);

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

        $this->assertSame(1, $useCaseSpy->executeCount);
        $this->assertNotNull($useCaseSpy->lastRequest);
        $this->assertSame($timestamp, $useCaseSpy->lastRequest->date_from);
        $this->assertSame('allegro', $useCaseSpy->lastRequest->filter_order_source);
    }

    public function testInvokeHandlesInvalidDate(): void
    {
        $client = static::createClient();

        $useCaseSpy = new FetchOrdersUseCaseSpy();
        static::getContainer()->set(FetchOrdersUseCaseInterface::class, $useCaseSpy);

        $client->jsonRequest('POST', '/api/orders/fetch', [
            'date_from' => 'invalid-date',
        ]);

        $this->assertResponseStatusCodeSame(422);
        $this->assertSame(0, $useCaseSpy->executeCount);
    }
}
