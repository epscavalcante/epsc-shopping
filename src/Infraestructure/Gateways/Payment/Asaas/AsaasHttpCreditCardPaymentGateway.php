<?php

declare(strict_types=1);

namespace Src\Infraestructure\Gateways\Payment\Asaas;

use DateTimeInterface;
use Exception;
use GuzzleHttp\Psr7\Request;
use Src\Application\Gateways\Payment\CreditCard\CreditCardPaymentGateway;
use Src\Application\Gateways\Payment\CreditCard\CreditCardPaymentGatewayInput;
use Src\Application\Gateways\Payment\CreditCard\CreditCardPaymentGatewayOutput;
use Src\Infraestructure\Http\Client\HttpClient;
use Src\Infraestructure\Logger\Logger;

class AsaasHttpCreditCardPaymentGateway implements CreditCardPaymentGateway
{
    public function __construct(
        private readonly Logger $logger,
        private readonly HttpClient $httpClient,
    ) {}

    public function process(CreditCardPaymentGatewayInput $input): CreditCardPaymentGatewayOutput
    {
        $payment = $this->createPayment(
            input: $input
        );

        return new CreditCardPaymentGatewayOutput(
            gatewayName: 'ASAAS',
            gatewayTransactionId: $payment['paymentId'],
            creditCardToken: $payment['creditCardToken'],
            creditCardBrand: $payment['creditCardBrand'],
            creditCardLastDigits: $payment['creditCardNumber']
        );
    }

    private function createPayment(CreditCardPaymentGatewayInput $input): array
    {
        $this->logger->debug('Assas CreditCard Input', (array) $input);

        $customerId = $this->createCustomer(
            name: $input->customerName,
            cpfCnpj: $input->customerDocumentValue
        );

        $creditCard = $this->createCreditCardToken(
            customerId: $customerId,
            creditCardHolderName: $input->creditCardHolderName,
            creditCardNumber: $input->creditCardNumber,
            creditCardExpiryMonth: $input->creditCardExpiryMonth,
            creditCardExpiryYear: $input->creditCardExpiryYear,
            creditCardCCV: $input->creditCardExpiryCCV,
            creditCardHolderInfoName: $input->creditCardHolderName,
            creditCardHolderInfoEmail: $input->creditCardHolderEmail,
            creditCardHolderInfoCpfCnpj: $input->creditCardHolderDocumentValue,
            creditCardHolderInfoPostalCode: $input->creditCardHolderAddressPostalCode,
            creditCardHolderInfoAddressNumber: $input->creditCardHolderAddressNumber,
            creditCardHolderInfoAddressComplement: $input->creditCardHolderAddressComplement,
            creditCardHolderInfoPhone: $input->creditCardHolderPhone,
            creditCardHolderInfoMobilePhone: $input->creditCardHolderPhone,
        );

        $paymentId = $this->createPaymentWithCreditCardToken(
            customerId: $customerId,
            amount: $input->amount,
            dueDate: $input->dueDate,
            creditCardToken: $creditCard['creditCardToken'],
        );

        return [
            'paymentId' => $paymentId,
            'creditCardToken' => $creditCard['creditCardToken'],
            'creditCardBrand' => $creditCard['creditCardBrand'],
            'creditCardNumber' => $creditCard['creditCardNumber'],
        ];
    }

    private function createPaymentWithCreditCardToken(string $customerId, int $amount, DateTimeInterface $dueDate, string $creditCardToken): string
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
                    "customer" => $customerId,
                    "billingType" => 'CREDIT_CARD',
                    "customer" => $customerId,
                    "value" => $amount,
                    "dueDate" => $dueDate->format('Y-m-d'),
                    "creditCardToken" => $creditCardToken,
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
                message: 'Asaas - CreditCard Payment created',
                context: [
                    'response' => $data
                ]
            );

            return $data['id'];
        }

        $this->logger->error(
            message: 'Erro ao cobrança via CC',
            context: [
                'response' => $response->getBody()->getContents()
            ]
        );

        throw new Exception('Erro ao criar CC payment');
    }

    private function createCreditCardToken(
        string $customerId,
        string $creditCardHolderName,
        string $creditCardNumber,
        string $creditCardExpiryMonth,
        string $creditCardExpiryYear,
        string $creditCardCCV,
        string $creditCardHolderInfoName,
        string $creditCardHolderInfoEmail,
        string $creditCardHolderInfoCpfCnpj,
        string $creditCardHolderInfoPostalCode,
        string $creditCardHolderInfoAddressNumber,
        string $creditCardHolderInfoPhone,
        string $creditCardHolderInfoMobilePhone,
        ?string $creditCardHolderInfoAddressComplement = null,
        ?string $remoteIp = '0.0.0.0'
    ): array {
        $request = new Request(
            method: 'POST',
            uri: 'https://api-sandbox.asaas.com/v3/creditCard/tokenize',
            headers: [
                "User-Agent" => "epsc-shopping",
                'Content-Type' => 'application/json',
                'access_token' => '$aact_hmlg_000MzkwODA2MWY2OGM3MWRlMDU2NWM3MzJlNzZmNGZhZGY6OmJlOWY4NDM5LWJjZmMtNGNkMi1iMzY5LTEwZDRiMzA5MWIyYzo6JGFhY2hfYzI1NjMyZDYtNzJlMC00ZDNmLTlhZWYtNGQ5ZGFmY2M4NzQ4'
            ],
            body: json_encode(
                value: [
                    "customer" => $customerId,
                    "creditCard" => [
                        "holderName" => $creditCardHolderName,
                        "number" => $creditCardNumber,
                        "expiryMonth" => $creditCardExpiryMonth,
                        "expiryYear" => $creditCardExpiryYear,
                        "ccv" => $creditCardCCV
                    ],
                    "creditCardHolderInfo" => [
                        "name" => $creditCardHolderInfoName,
                        "email" => $creditCardHolderInfoEmail,
                        "cpfCnpj" => $creditCardHolderInfoCpfCnpj,
                        "postalCode" => $creditCardHolderInfoPostalCode,
                        "addressNumber" => $creditCardHolderInfoAddressNumber,
                        "addressComplement" => $creditCardHolderInfoAddressComplement,
                        "phone" => $creditCardHolderInfoPhone,
                        "mobilePhone" => $creditCardHolderInfoMobilePhone,
                    ],
                    "remoteIp" => $remoteIp
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
                message: 'Asaas - CreditCardToen created',
                context: [
                    'response' => $data
                ]
            );

            return $data;
        }

        $this->logger->error(
            message: 'Erro ao CreditCardToen',
            context: [
                'response' => $response->getBody()->getContents()
            ]
        );

        throw new Exception('Erro ao criar CreditCardToen');
    }

    private function createCustomer(string $name, string $cpfCnpj): string
    {
        return "cus_000006786668";
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
