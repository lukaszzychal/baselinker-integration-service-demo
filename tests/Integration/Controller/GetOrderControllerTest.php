<?php

declare(strict_types=1);

namespace App\Tests\Integration\Controller;

use App\Entity\Order;
use App\Repository\OrderRepositoryInterface;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class GetOrderControllerTest extends WebTestCase
{
    public function testGetOrderReturns404WhenNotFound(): void
    {
        $client = static::createClient();

        $client->request('GET', '/api/orders/99999');

        $this->assertResponseStatusCodeSame(404);
    }

    public function testGetOrderReturnsOrderWhenFound(): void
    {
        $client = static::createClient();
        /** @var OrderRepositoryInterface $repository */
        $repository = static::getContainer()->get(OrderRepositoryInterface::class);

        $order = new Order('ext-123', 'allegro', 'Jan Kowalski', '199.99');
        $repository->save($order, true);

        $client->request('GET', '/api/orders/' . $order->id);

        $this->assertResponseIsSuccessful();
        $content = (string) $client->getResponse()->getContent();
        $decoded = json_decode($content, true);
        $this->assertArrayHasKey('id', $decoded);
        $this->assertArrayHasKey('external_id', $decoded);
        $this->assertEquals('ext-123', $decoded['external_id']);
        $this->assertEquals('allegro', $decoded['marketplace']);
        $this->assertEquals('Jan Kowalski', $decoded['customer_name']);
        $this->assertEquals('199.99', $decoded['total_amount']);
    }
}
