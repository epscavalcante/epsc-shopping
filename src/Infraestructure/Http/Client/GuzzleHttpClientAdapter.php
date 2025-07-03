<?php

declare(strict_types=1);

namespace Src\Infraestructure\Http\Client;

use Src\Infraestructure\Http\Client\HttpClient;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use GuzzleHttp\Client;

class GuzzleHttpClientAdapter implements HttpClient
{
    private readonly Client $guzzle;

    public function __construct()
    {
        $this->guzzle = new Client();
    }

    public function sendRequest(RequestInterface $request): ResponseInterface
    {
        return $this->guzzle->sendRequest($request);
    }
}
