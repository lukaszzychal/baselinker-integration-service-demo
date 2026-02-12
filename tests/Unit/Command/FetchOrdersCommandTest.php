<?php

namespace App\Tests\Unit\Command;

use App\Command\FetchOrdersCommand;
use App\Message\FetchOrders;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Tester\CommandTester;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\MessageBusInterface;

class FetchOrdersCommandTest extends TestCase
{
    /** @var MessageBusInterface&\PHPUnit\Framework\MockObject\MockObject */
    private $messageBus;
    private CommandTester $commandTester;

    protected function setUp(): void
    {
        $this->messageBus = $this->createMock(MessageBusInterface::class);
        $command = new FetchOrdersCommand($this->messageBus);
        $this->commandTester = new CommandTester($command);
    }

    public function testExecuteDispatchesMessageWithDefaultOptions(): void
    {
        $this->messageBus->expects($this->once())
            ->method('dispatch')
            ->with($this->callback(function (FetchOrders $message) {
                // Default date is -1 day, check if it's roughly correct (ignoring milliseconds slightly)
                $expectedDate = (new \DateTimeImmutable('-1 day'))->format('Y-m-d');
                return $message->from->format('Y-m-d') === $expectedDate
                    && $message->marketplace === 'all';
            }))
            ->willReturn(new Envelope(new \stdClass()));

        $this->commandTester->execute([]);

        $this->commandTester->assertCommandIsSuccessful();
        $output = $this->commandTester->getDisplay();
        $this->assertStringContainsString('Orders fetch command dispatched successfully!', $output);
    }

    public function testExecuteDispatchesMessageWithCustomOptions(): void
    {
        $customDate = '2023-12-31';
        $customMarketplace = 'allegro';

        $this->messageBus->expects($this->once())
            ->method('dispatch')
            ->with($this->callback(function (FetchOrders $message) use ($customDate, $customMarketplace) {
                return $message->from->format('Y-m-d') === $customDate
                    && $message->marketplace === $customMarketplace;
            }))
            ->willReturn(new Envelope(new \stdClass()));

        $this->commandTester->execute([
            '--from' => $customDate,
            '--marketplace' => $customMarketplace,
        ]);

        $this->commandTester->assertCommandIsSuccessful();
    }
    
    public function testExecuteReturnsFailureOnInvalidDate(): void
    {
        $this->messageBus->expects($this->never())->method('dispatch');

        $this->commandTester->execute([
            '--from' => 'invalid-date',
        ]);
        
        $this->assertNotEquals(0, $this->commandTester->getStatusCode());
        $output = $this->commandTester->getDisplay();
        $this->assertStringContainsString('Invalid date format', $output);
    }
}
