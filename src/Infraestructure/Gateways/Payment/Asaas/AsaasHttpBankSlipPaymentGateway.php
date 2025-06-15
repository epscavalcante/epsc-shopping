<?php

declare(strict_types=1);

namespace Src\Infraestructure\Gateways\Payment\Asaas;

use DateTimeInterface;
use Exception;
use GuzzleHttp\Psr7\Request;
use Src\Application\Gateways\Payment\BankSlip\BankSlipPaymentGateway;
use Src\Application\Gateways\Payment\BankSlip\BankSlipPaymentGatewayInput;
use Src\Application\Gateways\Payment\BankSlip\BankSlipPaymentGatewayOutput;
use Src\Infraestructure\Http\Client\HttpClient;
use Src\Infraestructure\Logger\Logger;

class AsaasHttpBankSlipPaymentGateway implements BankSlipPaymentGateway
{
    public function __construct(
        private readonly Logger $logger,
        private readonly HttpClient $httpClient,
    ) {}

    public function process(BankSlipPaymentGatewayInput $input): BankSlipPaymentGatewayOutput
    {
        $this->logger->debug('Assas BankSlip Input', (array) $input);
        $customerId = $this->createCustomer(
            name: $input->customerName, 
            cpfCnpj: $input->customerDocumentValue
        );

        $paymentId = $this->createBankSlip(
            customerId: $customerId,
            amount: $input->amount,
            dueDate: $input->dueDate,
        );
        $bankSlipInfo = $this->getBankSlipData(
            paymentId: $paymentId,
        );

        return new BankSlipPaymentGatewayOutput(
            gatewayName: 'ASAAS',
            gatewayTransactionId: $paymentId,
            barCode: $bankSlipInfo['bar_code'],
        );
    }

    private function getBankSlipData(string $paymentId): array
    {
        $request = new Request(
            method: 'GET',
            uri: "https://api-sandbox.asaas.com/v3/payments/{$paymentId}/identificationField",
            headers: [
                "User-Agent" => "epsc-shopping",
                'Content-Type' => 'application/json',
                'access_token' => '$aact_hmlg_000MzkwODA2MWY2OGM3MWRlMDU2NWM3MzJlNzZmNGZhZGY6OmJlOWY4NDM5LWJjZmMtNGNkMi1iMzY5LTEwZDRiMzA5MWIyYzo6JGFhY2hfYzI1NjMyZDYtNzJlMC00ZDNmLTlhZWYtNGQ5ZGFmY2M4NzQ4'
            ],
        );
        $response = $this->httpClient->sendRequest($request);

        if ($response->getStatusCode() === 200) {
            $data = json_decode(
                json: $response->getBody()->getContents(),
                associative: true
            );

            $this->logger->debug(
                message: 'Asaas - Pix QrCode info',
                context: [
                    'response' => $data
                ]
            );

            return [
                'bar_code' => $data['barCode'],
                'identification_number' => $data['identificationField'],
                'our_number' => $data['nossoNumero']
            ];
        }

        $this->logger->error(
            message: 'Erro ao criar customer',
            context: [
                'response' => $response->getBody()->getContents()
            ]
        );

        throw new Exception('Erro ao buscar dados do boleot');
    }

    private function createBankSlip(string $customerId, int $amount, DateTimeInterface $dueDate)
    {
        $request = new Request(
            method: 'POST',
            uri: 'https://api-sandbox.asaas.com/v3/payments',
            headers: [
                "User-Agent" => "epsc-shopping",
                'Content-Type' => 'application/json',
                'access_token' => '$aact_hmlg_000MzkwODA2MWY2OGM3MWRlMDU2NWM3MzJlNzZmNGZhZGY6OmJlOWY4NDM5LWJjZmMtNGNkMi1iMzY5LTEwZDRiMzA5MWIyYzo6JGFhY2hfYzI1NjMyZDYtNzJlMC00ZDNmLTlhZWYtNGQ5ZGFmY2M4NzQ4'
            ],
            body: json_encode(
                value: [

                    "billingType" => 'BOLETO',
                    "customer" => $customerId,
                    "value" => $amount,
                    "dueDate" => $dueDate->format('Y-m-d'),
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
                message: 'Asaas - BankSlip Payment created',
                context: [
                    'response' => $data
                ]
            );

            return $data['id'];
        }

        $this->logger->error(
            message: 'Erro ao criar boleto',
            context: [
                'response' => $response->getBody()->getContents()
            ]
        );

        throw new Exception('Erro ao criar boleto payment');
    }

    private function createCustomer(string $name, string $cpfCnpj): string 
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

                    "name" => $name,
                    "cpfCnpj" => $cpfCnpj,
                    //"email" => $request->email,
                    //"mobilePhone" => $request->phone,
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

            return $data['id'];
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
