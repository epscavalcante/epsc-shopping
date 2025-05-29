<?php

use Src\Domain\Entities\Order;
use Src\Domain\Entities\OrderCustomer;
use Src\Domain\Entities\Product;
use Src\Domain\Enums\OrderStatusEnum;

describe('Order Tests', function () {
    test('Deve criar um pedido com itens vazio', function () {
        $customer = new OrderCustomer(
            name: 'John Doe',
            email: 'email@email.com',
            phone: '00000000000'
        );
        $order = new Order(
            orderId: uniqid('ORDER_'),
            customer: $customer
        );
        
        expect($order->getTotal())->toBe(0);
        expect($order->getStatus())->toBe(OrderStatusEnum::CREATED->value);
    });

    test('Deve criar um pedido com 2 items', function () {
        $customer = new OrderCustomer(
            name: 'John Doe',
            email: 'email@email.com',
            phone: '00000000000'
        );
        $order = new Order(
            orderId: uniqid('ORDER_'),
            customer: $customer
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
        
        expect($order->getTotal())->toBe(1100);
        expect($order->getStatus())->toBe(OrderStatusEnum::CREATED->value);
    });
});