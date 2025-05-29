<?php

use Src\Domain\Entities\Product;

describe('Product Test', function () {
    test('Deve criar um produto', function () {
        $productId = uniqid('PROD_');
        $product = new Product(
            productId: $productId,
            name: 'Produto 1',
            price: 200,
        );

        expect($product->getId())->toBe($productId);
        expect($product->getPrice())->toBe(200);
        expect($product->getName())->toBe('Produto 1');
    });
});
