<?php

declare(strict_types=1);

namespace App\Tests\Unit\Controller;

use App\Controller\ListOrderSourcesController;
use App\Tests\Doubles\BaselinkerClientStub;
use PHPUnit\Framework\TestCase;

class ListOrderSourcesControllerTest extends TestCase
{
    public function testInvokeReturnsFlattenedSources(): void
    {
        $client = new BaselinkerClientStub(
            orderSourcesResponse: [
                'status' => 'SUCCESS',
                'sources' => [
                    'personal' => [0 => 'Personal'],
                    'shop' => [123 => 'My Shop'],
                    'marketplace' => [456 => 'Allegro', 789 => 'Amazon'],
                ],
            ],
        );

        $controller = new ListOrderSourcesController($client);

        $response = $controller->__invoke();

        /** @var array<string, mixed> $data */
        $data = json_decode((string) $response->getContent(), true);

        $this->assertArrayHasKey('sources', $data);
        $this->assertCount(4, $data['sources']);

        $names = array_column($data['sources'], 'name');
        $this->assertContains('Personal', $names);
        $this->assertContains('Allegro', $names);
        $this->assertContains('Amazon', $names);
        $this->assertContains('My Shop', $names);

        // Verify types
        $types = array_column($data['sources'], 'type');
        $this->assertContains('personal', $types);
        $this->assertContains('shop', $types);
        $this->assertContains('marketplace', $types);
    }

    public function testInvokeReturnsEmptyOnNoSources(): void
    {
        $client = new BaselinkerClientStub(
            orderSourcesResponse: [
                'status' => 'SUCCESS',
                'sources' => [],
            ],
        );

        $controller = new ListOrderSourcesController($client);
        $response = $controller->__invoke();

        /** @var array<string, mixed> $data */
        $data = json_decode((string) $response->getContent(), true);

        $this->assertSame([], $data['sources']);
    }
}
