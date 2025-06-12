<?php

declare(strict_types=1);

namespace Src\Application\UseCases\PlaceOrder;

use Exception;
use Src\Domain\Entities\Order;
use Src\Domain\Entities\OrderCustomer;
use Src\Domain\Repositories\OrderRepository;
use Src\Domain\Repositories\ProductRepository;

class PlaceOrder
{
    public function __construct(
        private readonly ProductRepository $productRepository,
        private readonly OrderRepository $orderRepository,
    ) {}

    public function execute(PlaceOrderInput $input): PlaceOrderOutput
    {
        $customer = new OrderCustomer(
            email: $input->customer->email,
            name: $input->customer->name,
            phone: $input->customer->phone
        );

        $order = new Order(
            orderId: uniqid('ORDER_ID_', true),
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
