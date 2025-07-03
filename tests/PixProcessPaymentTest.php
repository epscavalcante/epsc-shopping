<?php

use Src\Domain\Entities\Order;
use Src\Domain\Entities\OrderCustomer;
use Src\Infraestructure\Logger\MonologAdapter;
use Src\Application\UseCases\ProcessPayment\Pix\PixProcessPayment;
use Src\Application\UseCases\ProcessPayment\Pix\PixProcessPaymentInput;
use Src\Application\UseCases\ProcessPayment\Pix\PixProcessPaymentOutput;
use Src\Domain\Entities\Payment;
use Src\Domain\Entities\Product;
use Src\Infraestructure\Gateways\Payment\AbacatePay\AbacatePayHttpPixPaymentGateway;
use Src\Infraestructure\Http\Client\GuzzleHttpClientAdapter;
use Src\Infraestructure\Repositories\OrderDatabaseRepository;
use Src\Infraestructure\Repositories\PaymentDatabaseRepository;

describe('Pix - Process Payment Tests', function () {
    it('Deve processar o pagamento com PIX utilizando Asaas Gateway', function () {
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

        /*
        $pixPaymentGateway = new AsaasHttpPixPaymentGateway(
            logger: $logger,
            httpClient: $httpClient,
        );
        */

        $pixPaymentGateway = new AbacatePayHttpPixPaymentGateway(
            logger: $logger,
            httpClient: $httpClient,
        );

        /*
        $nextPaymentProcessor = new AbacatePayPixPaymentGatewayProcessor(
            pixPaymentGateway: $abacatePayPixPaymentGateway,
            logger: $logger,
        );
        $pixPaymentProcessor = new AsaasPixPaymentGatewayProcessor(
            pixPaymentGateway: $asaasPixPaymentGateway,
            nextPixPaymentProcessor: $nextPaymentProcessor,
            logger: $logger,
        );
        */
        $processPayment = new PixProcessPayment(
            pixPaymentGateway: $pixPaymentGateway,
            orderRepository: $orderRepository,
            paymentRepository: $paymentRepository,
        );
        $processPaymentInput = new PixProcessPaymentInput(
            orderId: $order->getId(),
        );
        $processPaymentOuput = $processPayment->execute($processPaymentInput);

        // dd($processPaymentOuput);

        expect($processPaymentOuput)->toBeInstanceOf(PixProcessPaymentOutput::class);
        expect($processPaymentOuput->paymentId)->toBeString();
    });

    it('Deve processar o pagamento com PIX utilizando AbacatePay Gateway', function () {
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

        $pixPaymentGateway = new AbacatePayHttpPixPaymentGateway(
            logger: $logger,
            httpClient: $httpClient,
        );

        $paymentRepository = new PaymentDatabaseRepository();

        $processPayment = new PixProcessPayment(
            pixPaymentGateway: $pixPaymentGateway,
            orderRepository: $orderRepository,
            paymentRepository: $paymentRepository,
        );
        $processPaymentInput = new PixProcessPaymentInput(
            orderId: $order->getId(),
        );
        $processPaymentOuput = $processPayment->execute($processPaymentInput);
        expect($processPaymentOuput)->toBeInstanceOf(PixProcessPaymentOutput::class);
        expect($processPaymentOuput->paymentId)->toBeString();

        $payment = $paymentRepository->getByIdOrFail($processPaymentOuput->paymentId);
        expect($payment)->toBeInstanceOf(Payment::class);
        expect($payment->isPixPaymentMethod())->toBe(true);
        expect($payment->getGatewayName())->toBe('ABACATE_PAY');
        expect($payment->getGatewayTransactionId())->toBeString();

    });

    it('Deve processar o pagamento com PIX utilizando Asaas Gateway Processor', function () {
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

        $pixPaymentGateway = new AbacatePayHttpPixPaymentGateway(
            logger: $logger,
            httpClient: $httpClient,
        );

        /*
        $nextPaymentProcessor = new AbacatePayPixPaymentGatewayProcessor(
            pixPaymentGateway: $abacatePayPixPaymentGateway,
            logger: $logger,
        );
        $pixPaymentProcessor = new AsaasPixPaymentGatewayProcessor(
            pixPaymentGateway: $asaasPixPaymentGateway,
            nextPixPaymentProcessor: $nextPaymentProcessor,
            logger: $logger,
        );
        */
        $processPayment = new PixProcessPayment(
            pixPaymentGateway: $pixPaymentGateway,
            orderRepository: $orderRepository,
            paymentRepository: $paymentRepository,
        );
        $processPaymentInput = new PixProcessPaymentInput(
            orderId: $order->getId(),
        );
        $processPaymentOuput = $processPayment->execute($processPaymentInput);

        // dd($processPaymentOuput);

        expect($processPaymentOuput)->toBeInstanceOf(PixProcessPaymentOutput::class);
        expect($processPaymentOuput->paymentId)->toBeString();
    })->skip();

    it('Deve processar o pagamento com PIX utilizando AbacatePay Gateway Processor', function () {
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

        $pixPaymentGateway = new AbacatePayHttpPixPaymentGateway(
            logger: $logger,
            httpClient: $httpClient,
        );

        /*
        $nextPaymentProcessor = new AbacatePayPixPaymentGatewayProcessor(
            pixPaymentGateway: $abacatePayPixPaymentGateway,
            logger: $logger,
        );
        $pixPaymentProcessor = new AsaasPixPaymentGatewayProcessor(
            pixPaymentGateway: $asaasPixPaymentGateway,
            nextPixPaymentProcessor: $nextPaymentProcessor,
            logger: $logger,
        );
        */
        $processPayment = new PixProcessPayment(
            pixPaymentGateway: $pixPaymentGateway,
            orderRepository: $orderRepository,
            paymentRepository: $paymentRepository,
        );
        $processPaymentInput = new PixProcessPaymentInput(
            orderId: $order->getId(),
        );
        $processPaymentOuput = $processPayment->execute($processPaymentInput);

        // dd($processPaymentOuput);

        expect($processPaymentOuput)->toBeInstanceOf(PixProcessPaymentOutput::class);
        expect($processPaymentOuput->paymentId)->toBeString();
    })->skip();
});
