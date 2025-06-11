<?php

declare(strict_types=1);

namespace Src\Infraestructure\Gateways\Payment\Asaas\Customer\CreateCustomer;

use Exception;
use GuzzleHttp\Psr7\Request;
use Src\Infraestructure\Http\Client\HttpClient;
use Src\Infraestructure\Logger\Logger;

class AsaasCreateCustomer
{
    public function __construct(
        private readonly Logger $logger,
        private readonly HttpClient $httpClient,
    ) {}

    public function process(AssasCreateCustomerRequest $request): AsaasCreateCustomerResponse
    {
        $request = new Request(
            method: 'POST',
            uri: 'https://api-sandbox.asaas.com/v3/customers',
            headers: [
                "User-Agent" => "epsc-shopping",
                'Content-Type' => 'application/json',
                'access_token' => '$aact_hmlg_000MzkwODA2MWY2OGM3MWRlMDU2NWM3MzJlNzZmNGZhZGY6OmJlOWY4NDM5LWJjZmMtNGNkMi1iMzY5LTEwZDRiMzA5MWIyYzo6JGFhY2hfYzI1NjMyZDYtNzJlMC00ZDNmLTlhZWYtNGQ5ZGFmY2M4NzQ4'
            ],
            body: json_encode(
                value: [

                    "name" => $request->name,
                    "cpfCnpj" => $request->cpfCnpj,
                    "email" => $request->email,
                    "mobilePhone" => $request->phone,
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
                message: 'Asaas - Customer created',
                context: [
                    'response' => $data
                ]
            );

            return new AsaasCreateCustomerResponse(
                customerId: $data['id'],
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
