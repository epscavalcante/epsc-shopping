<?php

use Src\Infraestructure\Gateways\Payment\Asaas\Pix\CreatePixPayment\AsaasCreatePixPayment;
use Src\Infraestructure\Gateways\Payment\Asaas\Pix\CreatePixPayment\AsaasCreatePixPaymentRequest;
use Src\Infraestructure\Gateways\Payment\Asaas\Pix\CreatePixPayment\AsaasCreatePixPaymentResponse;
use Src\Infraestructure\Http\Client\GuzzleHttpClientAdapter;
use Src\Infraestructure\Logger\MonologAdapter;

describe('Asaas - Payment Gateway', function () {
    it('Deve criar um um Pix (dinamico', function () {
        $logger = new MonologAdapter();
        $httpClient = new GuzzleHttpClientAdapter();
        $gateway = new AsaasCreatePixPayment(
            logger: $logger,
            httpClient: $httpClient
        );
        $date = new DateTime();
        $input = new AsaasCreatePixPaymentRequest(
            customer: 'cus_000006768126',
            dueDate: $date->modify('+2 day'),
            amount: 100,
        );
        $output = $gateway->process($input);
        expect($output)->toBeInstanceOf(AsaasCreatePixPaymentResponse::class);
        expect($output->paymentId)->toBeString();
    })->only();
});