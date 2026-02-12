<?php

declare(strict_types=1);

namespace App\Integration;

use Symfony\Contracts\HttpClient\HttpClientInterface;

final class BaselinkerClient implements BaselinkerClientInterface
{
    private const API_URL = 'https://api.baselinker.com/connector.php';

    public function __construct(
        private HttpClientInterface $httpClient,
        private string $token,
    ) {
    }

    /** @return array<string, mixed> */
    public function getOrders(\DateTimeInterface $from, array $filters = []): array
    {
        return $this->request('getOrders', array_merge(
            ['date_from' => $from->getTimestamp()],
            $filters,
        ));
    }

    /** @return array<string, mixed> */
    public function getOrderSources(): array
    {
        return $this->request('getOrderSources', []);
    }

    /** @return array<string, mixed> */
    public function getOrderStatusList(): array
    {
        return $this->request('getOrderStatusList', []);
    }

    /**
     * @param array<string, mixed> $params
     *
     * @return array<string, mixed>
     */
    private function request(string $method, array $params): array
    {
        $response = $this->httpClient->request('POST', self::API_URL, [
            'headers' => [
                'X-BLToken' => $this->token,
            ],
            'body' => [
                'method' => $method,
                'parameters' => json_encode($params),
            ],
        ]);

        /** @var array<string, mixed> $data */
        $data = $response->toArray();

        if (($data['status'] ?? '') !== 'SUCCESS') {
            throw new \RuntimeException('Baselinker API error: ' . (string) json_encode($data));
        }

        return $data;
    }
}
