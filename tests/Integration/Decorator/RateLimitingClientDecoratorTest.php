<?php

declare(strict_types=1);

namespace App\Tests\Integration\Decorator;

use App\Integration\BaselinkerClientInterface;
use App\Integration\Decorator\LoggingClientDecorator;
use App\Integration\Decorator\RateLimitingClientDecorator;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class RateLimitingClientDecoratorTest extends KernelTestCase
{
    public function testDecoratorChainIsWiredCorrectly(): void
    {
        self::bootKernel();

        $service = self::getContainer()->get(BaselinkerClientInterface::class);

        // Decorator chain: Logging(outermost) -> RateLimiting -> BaselinkerClient(innermost)
        // Logging (priority 10) applied last = outermost
        // RateLimiting (priority 20) applied first = inner
        $this->assertInstanceOf(LoggingClientDecorator::class, $service);

        // Verify RateLimiting is in the chain via Reflection
        $ref = new \ReflectionProperty(LoggingClientDecorator::class, 'client');
        $inner = $ref->getValue($service);
        $this->assertInstanceOf(RateLimitingClientDecorator::class, $inner);
    }
}
