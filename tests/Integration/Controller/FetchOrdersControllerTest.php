<?php

namespace App\Tests\Integration\Controller;

use App\Message\FetchOrders;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\MessageBusInterface;

class FetchOrdersControllerTest extends WebTestCase
{
    public function testInvokeDispatchesMessage(): void
    {
        $client = static::createClient();
        
        // Mock the message bus to verify dispatch and prevent actual handling
        $bus = $this->createMock(MessageBusInterface::class);
        $bus->expects($this->once())
            ->method('dispatch')
            ->with($this->callback(function ($message) {
                return $message instanceof FetchOrders 
                    && $message->marketplace === 'allegro'
                    && $message->from->format('Y-m-d') === '2023-01-01';
            }))
            ->willReturn(new Envelope(new \stdClass())); // MessageBusInterface::dispatch returns Envelope

        // Only works if service is public or accessible via test container
        // MessageBusInterface is usually available in test env
        // Note: Creating client boots the kernel, so we must access container AFTER createClient
        static::getContainer()->set(MessageBusInterface::class, $bus);

        $client->jsonRequest('POST', '/api/fetch-orders', [
            'from' => '2023-01-01',
            'marketplace' => 'allegro',
        ]);

        $this->assertResponseStatusCodeSame(202);
        
        $content = $client->getResponse()->getContent();
        $this->assertJson($content);
        $decoded = json_decode($content, true);
        $this->assertArrayHasKey('status', $decoded);
        $this->assertEquals('Order fetch job dispatched', $decoded['status']);
    }

    public function testInvokeHandlesInvalidDate(): void
    {
        $client = static::createClient();
        
        // Bus should NOT be called
        $bus = $this->createMock(MessageBusInterface::class);
        $bus->expects($this->never())->method('dispatch');
        static::getContainer()->set(MessageBusInterface::class, $bus);

        $client->jsonRequest('POST', '/api/fetch-orders', [
            'from' => 'invalid-date',
        ]);

        $this->assertResponseStatusCodeSame(400);
    }
}
