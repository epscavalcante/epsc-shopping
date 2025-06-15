<?php

use Src\Domain\Entities\Order;
use Src\Domain\Entities\OrderCustomer;
use Src\Domain\Entities\Payment;
use Src\Domain\Entities\Product;
use Src\Infraestructure\Repositories\OrderDatabaseRepository;
use Src\Infraestructure\Repositories\PaymentDatabaseRepository;

describe('Payment Repository Database Tests', function () {
    it('Deve falhar ao não encontrar o pagamento', function () {
        $repository = new PaymentDatabaseRepository();
        $repository->getByIdOrFail('fake');
    })->throws(Exception::class, 'Pagamento not found');

    it('Deve retornar null ao não encontrar um pagamento', function () {
        $repository = new OrderDatabaseRepository();
        $order = $repository->getById('fake');
        expect($order)->toBeNull();
    });

    it('Deve salvar um pagamento pix', function () {
        $customer = new OrderCustomer(
            name: 'John Doe',
            email: 'john.email@email.com',
            phone: '00000000000'
        );

        $product1 = new Product(
            productId: 'prod_1',
            name: 'Product 1',
            price: 1000
        );

        $product2 = new Product(
            productId: 'prod_2',
            name: 'Product 2',
            price: 3450
        );

        $order = new Order(
            orderId: uniqid('ORDER_', true),
            customer: $customer,
        );

        $order->addItem($product1, 1);
        $order->addItem($product2, 2);

        $orderRepository = new OrderDatabaseRepository();
        $orderRepository->create($order);

        $payment = Payment::create(
            orderId: $order->getId(),
            amount: $order->getTotal(),
            paymentMethod: 'PIX',
            dueDate: (new DateTime())->modify('+3 day'),
            gatewayName: 'FAKE',
            gatewayTransactionId: 'TRANSACTION_ID_FAKE',
            pixQrCode: 'QR_CODE',
            pixCopyPaste: 'CODIGO_PIX'
        );


        $paymentRepository = new PaymentDatabaseRepository();
        $paymentRepository->create($payment);

        $paymentCreated = $paymentRepository->getByIdOrFail($payment->getId());
        expect($paymentCreated)->toBeInstanceOf(Payment::class);
        expect($paymentCreated->getId())->toBeString();
        expect($paymentCreated->getOrderId())->toBe($order->getId());
        expect($paymentCreated->getAmount())->toBe($order->getTotal());
        expect($paymentCreated->getPaymentMethod())->toBe('PIX');
        expect($paymentCreated->getGatewayName())->toBe('FAKE');
        expect($paymentCreated->getGatewayTransactionId())->toBe('TRANSACTION_ID_FAKE');
        expect($paymentCreated->getPixQrCode())->toBe('QR_CODE');
        expect($paymentCreated->getPixCopyPaste())->toBe('CODIGO_PIX');
    });

    it('Deve salvar um pagamento boleto', function () {
        $customer = new OrderCustomer(
            name: 'John Doe',
            email: 'john.email@email.com',
            phone: '00000000000'
        );

        $product1 = new Product(
            productId: 'prod_1',
            name: 'Product 1',
            price: 1000
        );

        $product2 = new Product(
            productId: 'prod_2',
            name: 'Product 2',
            price: 3450
        );

        $order = new Order(
            orderId: uniqid('ORDER_', true),
            customer: $customer,
        );

        $order->addItem($product1, 1);
        $order->addItem($product2, 2);

        $orderRepository = new OrderDatabaseRepository();
        $orderRepository->create($order);
        $gatewayTransactionId = uniqid('TRANSACTION_ID_', true);
        $payment = Payment::create(
            orderId: $order->getId(),
            amount: $order->getTotal(),
            paymentMethod: 'BOLETO',
            dueDate: (new DateTime())->modify('+3 day'),
            gatewayName: 'FAKE',
            gatewayTransactionId: $gatewayTransactionId,
            barCode: 'BAR_CODE',
        );

        $paymentRepository = new PaymentDatabaseRepository();
        $paymentRepository->create($payment);
        $paymentCreated = $paymentRepository->getByIdOrFail($payment->getId());
        expect($paymentCreated)->toBeInstanceOf(Payment::class);
        expect($paymentCreated->getId())->toBeString();
        expect($paymentCreated->getOrderId())->toBe($order->getId());
        expect($paymentCreated->getAmount())->toBe($order->getTotal());
        expect($paymentCreated->getPaymentMethod())->toBe('BOLETO');
        expect($paymentCreated->getGatewayName())->toBe('FAKE');
        expect($paymentCreated->getGatewayTransactionId())->toBe($gatewayTransactionId);
        expect($paymentCreated->getBarCode())->toBe('BAR_CODE');
        expect($paymentCreated->getPixQrCode())->toBeNull();
        expect($paymentCreated->getPixCopyPaste())->toBeNull();
    });
});
