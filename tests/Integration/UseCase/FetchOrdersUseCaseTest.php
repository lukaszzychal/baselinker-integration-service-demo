<?php

declare(strict_types=1);

namespace App\Tests\Integration\UseCase;

use App\DTO\FetchOrdersRequest;
use App\Message\FetchOrders;
use App\UseCase\FetchOrdersUseCaseInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Zenstruck\Messenger\Test\InteractsWithMessenger;

class FetchOrdersUseCaseTest extends KernelTestCase
{
    use InteractsWithMessenger;

    public function testExecuteDispatchesMessageToTransport(): void
    {
        /** @var FetchOrdersUseCaseInterface $useCase */
        $useCase = self::getContainer()->get(FetchOrdersUseCaseInterface::class);
        $request = new FetchOrdersRequest(date_from: 1704067200); // 2024-01-01 00:00:00 UTC

        $useCase->execute($request);

        $queue = $this->transport('async')->queue();
        $queue->assertCount(1);
        $queue->assertContains(FetchOrders::class);

        /** @var FetchOrders $message */
        $message = $queue->first(FetchOrders::class)->getMessage();

        $this->assertEquals(1704067200, $message->from->getTimestamp());
        $this->assertEquals('all', $message->marketplace);
    }

    public function testExecuteDispatchesMultipleMessagesForCommaMarketplaces(): void
    {
        /** @var FetchOrdersUseCaseInterface $useCase */
        $useCase = self::getContainer()->get(FetchOrdersUseCaseInterface::class);
        $request = new FetchOrdersRequest(
            date_from: 1704067200,
            filter_order_source: 'allegro,amazon',
        );

        $useCase->execute($request);

        $queue = $this->transport('async')->queue();
        $queue->assertCount(2);

        $messages = [];
        foreach ($queue->all() as $envelope) {
            /** @var FetchOrders $msg */
            $msg = $envelope->getMessage();
            $messages[] = $msg->marketplace;
        }

        $this->assertContains('allegro', $messages);
        $this->assertContains('amazon', $messages);
    }

    public function testExecuteDispatchesSingleMessageForOneMarketplace(): void
    {
        /** @var FetchOrdersUseCaseInterface $useCase */
        $useCase = self::getContainer()->get(FetchOrdersUseCaseInterface::class);
        $request = new FetchOrdersRequest(
            date_from: 1704067200,
            filter_order_source: 'allegro',
        );

        $useCase->execute($request);

        $queue = $this->transport('async')->queue();
        $queue->assertCount(1);

        /** @var FetchOrders $message */
        $message = $queue->first(FetchOrders::class)->getMessage();
        $this->assertSame('allegro', $message->marketplace);
    }
}
