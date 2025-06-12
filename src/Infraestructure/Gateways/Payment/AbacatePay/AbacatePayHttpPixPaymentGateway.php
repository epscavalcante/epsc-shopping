<?php

declare(strict_types=1);

namespace Src\Infraestructure\Gateways\Payment\AbacatePay;

use DateTime;
use Exception;
use GuzzleHttp\Psr7\Request;
use Src\Infraestructure\Logger\Logger;
use Src\Application\Gateways\Payment\Pix\PixPaymentGateway;
use Src\Application\Gateways\Payment\Pix\PixPaymentGatewayInput;
use Src\Application\Gateways\Payment\Pix\PixPaymentGatewayOutput;
use Src\Infraestructure\Http\Client\HttpClient;

final class AbacatePayHttpPixPaymentGateway implements PixPaymentGateway
{
    public function __construct(
        private readonly Logger $logger,
        private readonly HttpClient $httpClient,
    ) {}

    public function process(PixPaymentGatewayInput $input): PixPaymentGatewayOutput
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
                    "amount" => $input->amount,
                    "expiresIn" => $input->dueDate->getTimestamp() - (new DateTime())->getTimestamp()
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

            return new PixPaymentGatewayOutput(
                gatewayName: 'ABACATE_PAY',
                gatewayTransactionId: $data['data']['id'],
                qrCode: $data['data']['brCodeBase64'], // podemos ter um problema, pois o asaas não fala que o valor está em base64
                copyPaste: $data['data']['brCodeBase64']
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
