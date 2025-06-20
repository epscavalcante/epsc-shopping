<?php

use Src\Infraestructure\Gateways\Payment\AbacatePay\Pix\CreatePixPayment\AbacatePayCreatePixPayment;
use Src\Infraestructure\Gateways\Payment\AbacatePay\Pix\CreatePixPayment\AbacatePayCreatePixPaymentRequest;
use Src\Infraestructure\Gateways\Payment\AbacatePay\Pix\CreatePixPayment\AbacatePayCreatePixPaymentResponse;
use Src\Infraestructure\Http\Client\GuzzleHttpClientAdapter;
use Src\Infraestructure\Logger\MonologAdapter;

describe('AbacatePay - Payment Gateway', function () {
    it('Deve criar um um Pix (dinamico', function () {
        $logger = new MonologAdapter();
        $httpClient = new GuzzleHttpClientAdapter();
        $gateway = new AbacatePayCreatePixPayment(
            logger: $logger,
            httpClient: $httpClient
        );
        $input = new AbacatePayCreatePixPaymentRequest(
            amount: 100,
            expiresIn: 300,
            description: 'Test'
        );
        $output = $gateway->process($input);
        expect($output)->toBeInstanceOf(AbacatePayCreatePixPaymentResponse::class);
        expect($output->paymentId)->toBeString();
    })->only();
});
