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
}
