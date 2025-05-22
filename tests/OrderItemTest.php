<?php

use Src\Domain\Entities\OrderItem;

describe('OrderItem Tests', function () {
    test('Não deve criar um order item com a quantidade inválida', function (int $quantity) {
        new OrderItem(
            productId: uniqid('PRODUCT_', true),
            price: 100,
            quantity: $quantity
        );
    })->throws(Exception::class, 'Invalid quantity')
        ->with([
            0,
            -1,
            -2
        ]);

    test('Deve criar um item do pedido', function () {
        $orderITem = new OrderItem(
            productId: uniqid('PRODUCT_', true),
            price: 100,
            quantity: 2
        );

        expect($orderITem->getTotal())->toBe(200);
    });
});