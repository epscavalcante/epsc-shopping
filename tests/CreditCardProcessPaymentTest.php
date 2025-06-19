<?php

use Src\Application\UseCases\ProcessPayment\CreditCard\CreditCardProcessPayment;
use Src\Application\UseCases\ProcessPayment\CreditCard\CreditCardProcessPaymentInput;
use Src\Application\UseCases\ProcessPayment\CreditCard\CreditCardProcessPaymentOutput;
use Src\Domain\Entities\Order;
use Src\Domain\Entities\OrderCustomer;
use Src\Infraestructure\Logger\MonologAdapter;
use Src\Domain\Entities\Product;
use Src\Infraestructure\Gateways\Payment\Asaas\AsaasHttpCreditCardPaymentGateway;
use Src\Infraestructure\Http\Client\GuzzleHttpClientAdapter;
use Src\Infraestructure\Repositories\OrderDatabaseRepository;
use Src\Infraestructure\Repositories\PaymentDatabaseRepository;

describe('Credit Card - Process Payment Tests', function () {
    it('Deve processar o pagamento com Credit Card utilizando Asaas Gateway', function () {
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
            productId: 'product_2',
            name: 'Produto 2',
            price: 200
        );
        $order->addItem($product2, 2);
        $orderRepository->create($order);

        $logger = new MonologAdapter();
        $httpClient = new GuzzleHttpClientAdapter();

        $creditCardPaymentGateway = new AsaasHttpCreditCardPaymentGateway(
            logger: $logger,
            httpClient: $httpClient,
        );

        $processPayment = new CreditCardProcessPayment(
            creditCardPaymentGateway: $creditCardPaymentGateway,
            orderRepository: $orderRepository,
            paymentRepository: $paymentRepository,
        );
        $processPaymentInput = new CreditCardProcessPaymentInput(
            orderId: $order->getId(),
            cardHolderName: 'Eduardo Cavalcante',
            cardNumber: '5421842424966831',
            cardExpiryMonth: '08',
            cardExpiryYear: '2026',
            cardCCV: '595',
            holderName: 'Eduardo Cavalcante',
            holderEmail: 'eduardo0310pvh@gmail.com',
            holderDocumentValue: '24971563792',
            holderPhone: '65993552122',
            holderAddressPostalCode: '89223-005',
            holderAddressNumber: '277',
            holderAddressComplement: null,
        );
        $processPaymentOuput = $processPayment->execute($processPaymentInput);

        expect($processPaymentOuput)->toBeInstanceOf(CreditCardProcessPaymentOutput::class);
        expect($processPaymentOuput->paymentId)->toBeString();
    });
});
