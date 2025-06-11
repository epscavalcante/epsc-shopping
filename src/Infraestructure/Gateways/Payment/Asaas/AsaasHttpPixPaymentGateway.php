<?php

declare(strict_types=1);

namespace Src\Infraestructure\Gateways\Payment\Asaas;

use DateTimeInterface;
use Exception;
use GuzzleHttp\Psr7\Request;
use Src\Application\Gateways\Payment\Pix\PixPaymentGateway;
use Src\Application\Gateways\Payment\Pix\PixPaymentGatewayInput;
use Src\Application\Gateways\Payment\Pix\PixPaymentGatewayOutput;
use Src\Infraestructure\Http\Client\HttpClient;
use Src\Infraestructure\Logger\Logger;

class AsaasHttpPixPaymentGateway implements PixPaymentGateway
{
    public function __construct(
        private readonly Logger $logger,
        private readonly HttpClient $httpClient,
        //private readonly AsaasCreateCustomer $createCustomer,
        //private readonly AsaasCreatePixPayment $createPixPayment,
    ) {}

    public function process(PixPaymentGatewayInput $input): PixPaymentGatewayOutput
    {
        $this->logger->debug('Assas Pix Input', (array) $input);
        $customerId = $this->createCustomer(
            name: $input->customerName, 
            cpfCnpj: $input->customerDocumentValue
        );

        $paymentId = $this->createPix(
            customerId: $customerId,
            amount: $input->amount,
            dueDate: $input->dueDate,
        );
        $pixInfo = $this->getPixData(
            paymentId: $paymentId,
        );

        return new PixPaymentGatewayOutput(
            gatewayName: 'ASAAS',
            gatewayTransactionId: $paymentId,
            qrCode: $pixInfo['pix_qr_code'],
            copyPaste: $pixInfo['pix_copy_paste'],
        );
    }

    private function getPixData(string $paymentId): array
    {
        $request = new Request(
            method: 'GET',
            uri: "https://api-sandbox.asaas.com/v3/payments/{$paymentId}/pixQrCode",
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
                'pix_qr_code' => $data['encodedImage'],
                'pix_copy_paste' => $data['payload']
            ];
        }

        $this->logger->error(
            message: 'Erro ao criar customer',
            context: [
                'response' => $response->getBody()->getContents()
            ]
        );

        throw new Exception('Erro ao buscar pix data');
    }

    private function createPix(string $customerId, int $amount, DateTimeInterface $dueDate)
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

                    "billingType" => 'PIX',
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
                message: 'Asaas - Pix Payment created',
                context: [
                    'response' => $data
                ]
            );

            return $data['id'];
        }

        $this->logger->error(
            message: 'Erro ao criar pix',
            context: [
                'response' => $response->getBody()->getContents()
            ]
        );

        throw new Exception('Erro ao criar pix payment');
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

    /*
    usando funções separadas
    public function process(PixPaymentGatewayInput $input): PixPaymentGatewayOutput
    {
        $createCustomerRequest = new AssasCreateCustomerRequest(
            name: 'Eduardo Cavalcante',
            cpfCnpj: '959.512.920-85',
            email: 'eduardo0310pvh@gmail.com',
            phone: '65993552122'
        );
        $createCustomerResponse = $this->createCustomer->process($createCustomerRequest);

        $createPixRequest = new AsaasCreatePixPaymentRequest(
            customer: $createCustomerResponse->customerId,
            amount: $input->amount,
            dueDate: $input->dueDate
        );

        $createPixResponse = $this->createPixPayment->process($createPixRequest);

        dd($createPixResponse);

        $this->logger->info(
            message: 'Process Pix With Asaas',
            context: [
                'input' => (array) $input,
            ]
        );

        return new PixPaymentGatewayOutput(
            gatewayName: 'ASAAS',
            gatewayTransactionId: $createPixResponse->paymentId,
            qrCode: 'BUSCAR QR_CODE',
            copyPaste: 'COPU_PASTE',
        );
    }
    */
}
