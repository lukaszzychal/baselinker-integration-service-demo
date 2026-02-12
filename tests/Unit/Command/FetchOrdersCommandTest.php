<?php

declare(strict_types=1);

namespace App\Tests\Unit\Command;

use App\Command\FetchOrdersCommand;
use App\Tests\Doubles\FetchOrdersUseCaseSpy;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Tester\CommandTester;

class FetchOrdersCommandTest extends TestCase
{
    private FetchOrdersUseCaseSpy $useCaseSpy;
    private CommandTester $commandTester;

    protected function setUp(): void
    {
        $this->useCaseSpy = new FetchOrdersUseCaseSpy();

        $command = new FetchOrdersCommand($this->useCaseSpy);
        $this->commandTester = new CommandTester($command);
    }

    public function testExecuteDispatchesMessageWithDefaultOptions(): void
    {
        $this->commandTester->execute([]);

        $this->commandTester->assertCommandIsSuccessful();
        $output = $this->commandTester->getDisplay();
        $this->assertStringContainsString('Orders fetch command dispatched successfully!', $output);

        $this->assertSame(1, $this->useCaseSpy->executeCount);
        $request = $this->useCaseSpy->lastRequest;
        $this->assertNotNull($request);

        $expectedDate = (new \DateTimeImmutable('-1 day'))->format('Y-m-d');
        $this->assertSame($expectedDate, date('Y-m-d', (int) $request->date_from));
        $this->assertNull($request->filter_order_source);
    }

    public function testExecuteDispatchesMessageWithCustomOptions(): void
    {
        $this->commandTester->execute([
            '--from' => '2023-12-31',
            '--marketplace' => 'allegro',
        ]);

        $this->commandTester->assertCommandIsSuccessful();

        $this->assertSame(1, $this->useCaseSpy->executeCount);
        $request = $this->useCaseSpy->lastRequest;
        $this->assertNotNull($request);
        $this->assertSame('2023-12-31', date('Y-m-d', (int) $request->date_from));
        $this->assertSame('allegro', $request->filter_order_source);
    }

    public function testExecuteReturnsFailureOnInvalidDate(): void
    {
        $this->commandTester->execute([
            '--from' => 'invalid-date',
        ]);

        $this->assertNotEquals(0, $this->commandTester->getStatusCode());
        $output = $this->commandTester->getDisplay();
        $this->assertStringContainsString('Invalid date format', $output);

        $this->assertSame(0, $this->useCaseSpy->executeCount);
    }
}
