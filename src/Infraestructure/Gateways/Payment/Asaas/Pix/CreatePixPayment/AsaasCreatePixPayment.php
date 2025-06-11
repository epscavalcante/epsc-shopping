<?php

declare(strict_types=1);

namespace Src\Infraestructure\Gateways\Payment\Asaas\Pix\CreatePixPayment;

use Exception;
use GuzzleHttp\Psr7\Request;
use Src\Infraestructure\Http\Client\HttpClient;
use Src\Infraestructure\Logger\Logger;

class AsaasCreatePixPayment
{
    public function __construct(
        private readonly Logger $logger,
        private readonly HttpClient $httpClient,
    ) {}

    public function process(AsaasCreatePixPaymentRequest $request): AsaasCreatePixPaymentResponse
    {
        $request = new Request(
            method: 'POST',
            uri: 'https://api-sandbox.asaas.com/v3/lean/payments',
            headers: [
                "User-Agent" => "epsc-shopping",
                'Content-Type' => 'application/json',
                'access_token' => '$aact_hmlg_000MzkwODA2MWY2OGM3MWRlMDU2NWM3MzJlNzZmNGZhZGY6OmJlOWY4NDM5LWJjZmMtNGNkMi1iMzY5LTEwZDRiMzA5MWIyYzo6JGFhY2hfYzI1NjMyZDYtNzJlMC00ZDNmLTlhZWYtNGQ5ZGFmY2M4NzQ4'
            ],
            body: json_encode(
                value: [

                    "customer" => $request->customer,
                    "billingType" => 'PIX',
                    "value" => $request->amount,
                    "dueDate" => $request->dueDate->format('Y-m-d'),
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
                message: 'Asaas - Pix Payment created',
                context: [
                    'response' => $data
                ]
            );

            return new AsaasCreatePixPaymentResponse(
                paymentId: $data['id'],
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
