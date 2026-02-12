<?php

declare(strict_types=1);

namespace App\Tests\Integration\Decorator;

use App\Integration\BaselinkerClientInterface;
use App\Integration\Decorator\LoggingClientDecorator;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class LoggingClientDecoratorTest extends KernelTestCase
{
    public function testServiceIsDecorated(): void
    {
        self::bootKernel();
        $container = static::getContainer();

        $client = $container->get(BaselinkerClientInterface::class);

        $this->assertInstanceOf(LoggingClientDecorator::class, $client);
    }
}
