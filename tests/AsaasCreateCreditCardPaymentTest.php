<?php

use Src\Infraestructure\Http\Client\GuzzleHttpClientAdapter;
use Src\Infraestructure\Logger\MonologAdapter;

describe('Asaas - Payment Gateway', function () {
    it('Deve criar um pagamento com Cartão de crédito', function () {
        $logger = new MonologAdapter();
        $httpClient = new GuzzleHttpClientAdapter();
        $gateway = new AsaasCreateCreditCardPayment(
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