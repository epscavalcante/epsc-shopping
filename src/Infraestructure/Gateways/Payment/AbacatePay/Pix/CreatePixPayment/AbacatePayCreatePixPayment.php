<?php

declare(strict_types=1);

namespace Src\Infraestructure\Gateways\Payment\AbacatePay\Pix\CreatePixPayment;

use Exception;
use GuzzleHttp\Psr7\Request;
use Src\Infraestructure\Http\Client\HttpClient;
use Src\Infraestructure\Logger\Logger;

class AbacatePayCreatePixPayment
{
    public function __construct(
        private readonly Logger $logger,
        private readonly HttpClient $httpClient,
    ) {}

    public function process(AbacatePayCreatePixPaymentRequest $request): AbacatePayCreatePixPaymentResponse
    {
        $request = new Request(
            method: 'POST',
            uri: 'https://api.abacatepay.com/v1/pixQrCode/create',
            headers: [
                'Content-Type' => 'application/json',
                'Authorization' => 'Bearer abc_dev_c2Td2Cq2UEuWYYTJMXzsZneX'
            ],
            body: json_encode(
                value: [
                    "amount" => $request->amount,
                    "expiresIn" => $request->expiresIn
                ]
            )
        );
        $response = $this->httpClient->sendRequest($request);

        if ($response->getStatusCode() === 200) {
            $data = json_decode(
                json: $response->getBody()->getContents(),
                associative: true
            );

            $this->logger->debug(
                message: 'AbacatePay - Pix Payment created',
                context: [
                    'response' => $data
                ]
            );

            return new AbacatePayCreatePixPaymentResponse(
                paymentId: $data['data']['id'],
                status: $data['data']['status'],
                copyPaste: $data['data']['brCode'],
                qrCode: $data['data']['brCodeBase64'],
            );
        }

        $this->logger->error(
            message: 'Erro ao criar customer',
            context: [
                'response' => $response->getBody()->getContents()
            ]
        );
        throw new Exception('Erro ao criar customer');
    }
}
