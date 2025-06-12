<?php

use Src\Infraestructure\Gateways\Payment\Asaas\Customer\CreateCustomer\AsaasCreateCustomer;
use Src\Infraestructure\Gateways\Payment\Asaas\Customer\CreateCustomer\AsaasCreateCustomerResponse;
use Src\Infraestructure\Gateways\Payment\Asaas\Customer\CreateCustomer\AssasCreateCustomerRequest;
use Src\Infraestructure\Http\Client\GuzzleHttpClientAdapter;
use Src\Infraestructure\Logger\MonologAdapter;

describe('Asaas - Payment Gateway', function () {
    it('Deve criar um customer', function () {
        $logger = new MonologAdapter();
        $httpClient = new GuzzleHttpClientAdapter();
        $gateway = new AsaasCreateCustomer(
            logger: $logger,
            httpClient: $httpClient
        );
        $input = new AssasCreateCustomerRequest(
            name: 'John Doe',
            cpfCnpj: '959.512.920-85',
        );
        $output = $gateway->process($input);
        expect($output)->toBeInstanceOf(AsaasCreateCustomerResponse::class);
        expect($output->customerId)->toBeString();
    })->only();
});