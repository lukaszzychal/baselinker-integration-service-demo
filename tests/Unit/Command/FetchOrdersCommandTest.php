<?php

declare(strict_types=1);

namespace App\Tests\Unit\Command;

use App\Command\FetchOrdersCommand;
use App\DTO\FetchOrdersRequest;
use App\UseCase\FetchOrdersUseCaseInterface;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Tester\CommandTester;

class FetchOrdersCommandTest extends TestCase
{
    /** @var FetchOrdersUseCaseInterface&\PHPUnit\Framework\MockObject\MockObject */
    private $useCase;
    private CommandTester $commandTester;

    protected function setUp(): void
    {
        $this->useCase = $this->createMock(FetchOrdersUseCaseInterface::class);

        $command = new FetchOrdersCommand($this->useCase);
        $this->commandTester = new CommandTester($command);
    }

    public function testExecuteDispatchesMessageWithDefaultOptions(): void
    {
        $this->useCase->expects($this->once())
            ->method('execute')
            ->with($this->callback(function (FetchOrdersRequest $request) {
                // Default date is -1 day
                $expectedDate = (new \DateTimeImmutable('-1 day'))->format('Y-m-d');

                return date('Y-m-d', (int) $request->date_from) === $expectedDate
                    && null === $request->filter_order_source; // 'all' becomes null in command logic
            }));

        $this->commandTester->execute([]);

        $this->commandTester->assertCommandIsSuccessful();
        $output = $this->commandTester->getDisplay();
        $this->assertStringContainsString('Orders fetch command dispatched successfully!', $output);
    }

    public function testExecuteDispatchesMessageWithCustomOptions(): void
    {
        $customDate = '2023-12-31';
        $customMarketplace = 'allegro';

        $this->useCase->expects($this->once())
            ->method('execute')
            ->with($this->callback(function (FetchOrdersRequest $request) use ($customDate, $customMarketplace) {
                return date('Y-m-d', (int) $request->date_from) === $customDate
                    && $request->filter_order_source === $customMarketplace;
            }));

        $this->commandTester->execute([
            '--from' => $customDate,
            '--marketplace' => $customMarketplace,
        ]);

        $this->commandTester->assertCommandIsSuccessful();
    }

    public function testExecuteReturnsFailureOnInvalidDate(): void
    {
        $this->useCase->expects($this->never())->method('execute');

        $this->commandTester->execute([
            '--from' => 'invalid-date',
        ]);

        $this->assertNotEquals(0, $this->commandTester->getStatusCode());
        $output = $this->commandTester->getDisplay();
        $this->assertStringContainsString('Invalid date format', $output);
    }
}
