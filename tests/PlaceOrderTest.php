<?php

use Src\Application\UseCases\PlaceOrder\PlaceOrder;
use Src\Application\UseCases\PlaceOrder\PlaceOrderInput;
use Src\Application\UseCases\PlaceOrder\PlaceOrderOutput;
use Src\Domain\Entities\Product;
use Src\Domain\Repositories\OrderRepository;
use Src\Domain\Repositories\ProductRepository;

describe('PlaceOrder Tests', function () {
    test('Deve Fazer um pedido conta existente', function () {
        $product = new Product(
            productId: 'product_id',
            name: 'Produto 1',
            price: 250
        );
        $productRepository = Mockery::mock(ProductRepository::class);
        $productRepository->shouldReceive('getById')
            ->once()
            ->andReturn($product);

        $orderRespository = Mockery::mock(OrderRepository::class);
        $orderRespository->shouldReceive('create')
            ->once()
            ->andReturn();

        $placeOrder = new PlaceOrder(
            productRepository: $productRepository,
            orderRepository: $orderRespository,
        );
        $input = PlaceOrderInput::create(
            customerName: 'John Doe',
            customerEmail: 'email@email.com',
            customerPhone: '00000000000',
            items: [
                [
                    'product_id' => 'product_1',
                    'quantity' => 1,
                ]
            ]
        );
        $output = $placeOrder->execute($input);
        expect($output)->toBeInstanceOf(PlaceOrderOutput::class);
    });
});
