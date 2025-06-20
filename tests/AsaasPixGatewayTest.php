<?php

use Src\Application\Gateways\Payment\Pix\PixPaymentGatewayInput;
use Src\Infraestructure\Gateways\Payment\Asaas\AsaasHttpPixPaymentGateway;
use Src\Infraestructure\Http\Client\GuzzleHttpClientAdapter;
use Src\Infraestructure\Logger\MonologAdapter;

describe('Asaas - Pix Http Payment Gateway', function () {
    it('Deve processar o pagamento de um pedido via PIX', function () {
        $logger = new MonologAdapter();
        $httpClient = new GuzzleHttpClientAdapter();
        $gateway = new AsaasHttpPixPaymentGateway(
            logger: $logger,
            httpClient: $httpClient
        );
        $input = new PixPaymentGatewayInput(
            amount: 10000,
        );
        $output = $gateway->process($input);
        dd($output);
        expect(true)->toBe(true);
    })->only();
});