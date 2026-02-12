<?php

declare(strict_types=1);

namespace App\Tests\Integration\Controller;

use App\Entity\Order;
use App\Repository\OrderRepositoryInterface;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class ListOrdersControllerTest extends WebTestCase
{
    public function testGetOrdersReturnsEmptyArrayWhenNoOrders(): void
    {
        $client = static::createClient();

        $client->request('GET', '/api/orders');

        $this->assertResponseIsSuccessful();
        $this->assertResponseStatusCodeSame(200);
        $content = (string) $client->getResponse()->getContent();
        $this->assertJson($content);
        $decoded = json_decode($content, true);
        $this->assertIsArray($decoded);
        $this->assertArrayHasKey('orders', $decoded);
        $this->assertEmpty($decoded['orders']);
    }

    public function testGetOrdersReturnsListOfOrders(): void
    {
        $client = static::createClient();
        /** @var OrderRepositoryInterface $repository */
        $repository = static::getContainer()->get(OrderRepositoryInterface::class);

        $repository->save(new Order('ext-1', 'allegro', 'Jan Kowalski', '199.99'), true);
        $repository->save(new Order('ext-2', 'amazon', 'Anna Nowak', '349.50'), true);

        $client->request('GET', '/api/orders');

        $this->assertResponseIsSuccessful();
        $content = (string) $client->getResponse()->getContent();
        $decoded = json_decode($content, true);
        $this->assertArrayHasKey('orders', $decoded);
        $this->assertCount(2, $decoded['orders']);

        $orders = $decoded['orders'];
        $this->assertArrayHasKey('id', $orders[0]);
        $this->assertArrayHasKey('external_id', $orders[0]);
        $this->assertArrayHasKey('marketplace', $orders[0]);
        $this->assertArrayHasKey('customer_name', $orders[0]);
        $this->assertArrayHasKey('total_amount', $orders[0]);
        $this->assertArrayHasKey('created_at', $orders[0]);
    }
}
