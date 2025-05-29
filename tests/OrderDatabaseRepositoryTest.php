<?php

use Src\Domain\Entities\Order;
use Src\Domain\Entities\OrderCustomer;
use Src\Domain\Entities\Product;
use Src\Infraestructure\Repositories\OrderDatabaseRepository;

describe('Order Repository Database Tests', function () {
    it('Deve falhar ao não encontrar um pedido', function () {
        $repository = new OrderDatabaseRepository();
        $repository->getByIdOrFail('fake');
    })->throws(Exception::class, 'Order not found');

    it('Deve retornar null ao não encontrar um pedido', function () {
        $repository = new OrderDatabaseRepository();
        $order = $repository->getById('fake');
        expect($order)->toBeNull();
    });

    it('Deve salvar um pedido', function () {
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

        $repository = new OrderDatabaseRepository();
        $repository->create($order);

        $orderCreated = $repository->getByIdOrFail($order->getId());
        expect($orderCreated)->toBeInstanceOf(Order::class);
        expect($orderCreated->getCustomerName())->toBe($customer->name);
        expect($orderCreated->getCustomerEmail())->toBe($customer->email);
        expect($orderCreated->getCustomerPhone())->toBe($customer->phone);
        expect($orderCreated->getTotal())->toBe(7900);

    });
});