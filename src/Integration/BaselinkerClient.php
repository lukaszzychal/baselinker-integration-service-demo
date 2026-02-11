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
    public function getOrders(\DateTimeInterface $from): array
    {
        return $this->request('getOrders', [
            'date_from' => $from->getTimestamp(),
        ]);
    }

    /**
     * @param array<string, mixed> $params
     *
     * @return array<string, mixed>
     */
    public function getOrderDetails(array $params): array
    {
        return $this->request('getOrderDetails', $params);
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
