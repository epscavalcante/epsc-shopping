<?php

use Src\Application\UseCases\PlaceOrder\PlaceOrder;
use Src\Application\UseCases\PlaceOrder\PlaceOrderInput;
use Src\Application\UseCases\PlaceOrder\PlaceOrderOutput;
use Src\Domain\Entities\Account;
use Src\Domain\Entities\Product;
use Src\Domain\Repositories\AccountRepository;
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

        $account = new Account(
            accountId: 'account_id',
            name: 'John Doe',
            email: 'email@email.com'
        );
        $accountRepository = Mockery::mock(AccountRepository::class);
        $accountRepository->shouldReceive('getById')
            ->once()
            ->andReturn($account);

        $placeOrder = new PlaceOrder(
            productRepository: $productRepository,
            orderRepository: $orderRespository,
            accountRepository: $accountRepository
        );
        $input = new PlaceOrderInput(
            accountId: $account->accountId,
            email: $account->email,
            phone: '00000000000',
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
