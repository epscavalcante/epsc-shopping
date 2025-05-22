<?php

declare(strict_types=1);

namespace Src\Application\UseCases\PlaceOrder;

use Exception;
use Src\Domain\Entities\Order;
use Src\Domain\Entities\OrderCustomer;
use Src\Domain\Repositories\AccountRepository;
use Src\Domain\Repositories\OrderRepository;
use Src\Domain\Repositories\ProductRepository;

class PlaceOrder
{
    public function __construct(
        private readonly ProductRepository $productRepository,
        private readonly OrderRepository $orderRepository,
        private readonly AccountRepository $accountRepository
    ) {}

    public function execute(PlaceOrderInput $input): PlaceOrderOutput
    {
        $account = null;
        if ($input->accountId) {
            $account = $this->accountRepository->getById($input->accountId);
            if (is_null($account)) {
                throw new Exception('Account not found');
            }
        }

        $customer = new OrderCustomer(
            accountId: $account->accountId ?? null,
            email: $account->email ?? null,
            phone: $input->phone
        );

        $order = new Order(
            orderId: 'order_id',
            customer: $customer
        );

        foreach ($input->items as $item) {
            $product = $this->productRepository->getById($item['product_id']);
            if (is_null($product)) {
                throw new Exception('Product not found');
            }

            $order->addItem($product, $item['quantity']);
        }

        $this->orderRepository->create($order);

        return new PlaceOrderOutput(
            orderId: $order->getId()
        );
    }
}
