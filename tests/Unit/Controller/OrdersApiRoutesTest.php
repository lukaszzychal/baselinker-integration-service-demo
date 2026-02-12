<?php

declare(strict_types=1);

namespace App\Tests\Unit\Controller;

use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Routing\RouterInterface;

/**
 * Testy weryfikujące poprawność tras REST API (bez bootowania DB).
 */
class OrdersApiRoutesTest extends KernelTestCase
{
    private RouterInterface $router;

    protected function setUp(): void
    {
        self::bootKernel();
        /** @var RouterInterface $router */
        $router = static::getContainer()->get(RouterInterface::class);
        $this->router = $router;
    }

    public function testFetchOrdersRouteExists(): void
    {
        $route = $this->router->getRouteCollection()->get('api_orders_fetch');
        $this->assertNotNull($route);
        $this->assertContains('POST', $route->getMethods());
        $this->assertStringContainsString('orders/fetch', $route->getPath());
    }

    public function testListOrdersRouteExists(): void
    {
        $route = $this->router->getRouteCollection()->get('api_orders_list');
        $this->assertNotNull($route);
        $this->assertContains('GET', $route->getMethods());
        $this->assertStringContainsString('orders', $route->getPath());
    }

    public function testGetOrderRouteExists(): void
    {
        $route = $this->router->getRouteCollection()->get('api_orders_show');
        $this->assertNotNull($route);
        $this->assertContains('GET', $route->getMethods());
        $this->assertStringContainsString('orders', $route->getPath());
    }
}
