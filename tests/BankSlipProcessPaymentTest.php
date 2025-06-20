<?php

use Src\Application\UseCases\ProcessPayment\BankSlip\BankSlipProcessPaymentInput;
use Src\Application\UseCases\ProcessPayment\BankSlip\BankSlipProcessPayment;
use Src\Application\UseCases\ProcessPayment\BankSlip\BankSlipProcessPaymentOutput;
use Src\Domain\Entities\Order;
use Src\Domain\Entities\OrderCustomer;
use Src\Infraestructure\Logger\MonologAdapter;
use Src\Domain\Entities\Product;
use Src\Infraestructure\Gateways\Payment\Asaas\AsaasHttpBankSlipPaymentGateway;
use Src\Infraestructure\Http\Client\GuzzleHttpClientAdapter;
use Src\Infraestructure\Repositories\OrderDatabaseRepository;
use Src\Infraestructure\Repositories\PaymentDatabaseRepository;

describe('Boleto - Process Payment Tests', function () {
    it('Deve processar o pagamento com Boleto utilizando Asaas Gateway', function () {
        $paymentRepository = new PaymentDatabaseRepository();
        $orderRepository = new OrderDatabaseRepository();
        $order = new Order(
            orderId: uniqid('ORDER_ID_', true),
            customer: new OrderCustomer(
                name: 'John Doe',
                email: 'john.doe@email.com',
                phone: '00000000000'
            ),
        );
        $product1 = new Product(
            productId: 'product_1',
            name: 'Produto 1',
            price: 350
        );
        $order->addItem($product1, 2);

        $product2 = new Product(
            productId:'product_2',
            name: 'Produto 2',
            price: 200
        );
        $order->addItem($product2, 2);
        $orderRepository->create($order);

        $logger = new MonologAdapter();
        $httpClient = new GuzzleHttpClientAdapter();

        $bankSlipPaymentGateway = new AsaasHttpBankSlipPaymentGateway(
            logger: $logger,
            httpClient: $httpClient,
        );

        $processPayment = new BankSlipProcessPayment(
            bankSlipPaymentGateway: $bankSlipPaymentGateway,
            orderRepository: $orderRepository,
            paymentRepository: $paymentRepository,
        );
        $processPaymentInput = new BankSlipProcessPaymentInput(
            orderId: $order->getId(),
        );
        $processPaymentOuput = $processPayment->execute($processPaymentInput);

        expect($processPaymentOuput)->toBeInstanceOf(BankSlipProcessPaymentOutput::class);
        expect($processPaymentOuput->paymentId)->toBeString();
        expect($processPaymentOuput->barCode)->toBeString();
    });
});
