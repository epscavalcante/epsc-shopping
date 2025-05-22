<?php

declare(strict_types=1);

namespace Src\Domain\Entities;

class Order
{
    /**
     * @var OrderItem[]
     */
    private array $items;

    public function __construct(
        private readonly string $orderId,
        private readonly OrderCustomer $customer,
    ) {
        $this->items = [];
    }

    public function getId(): string
    {
        return $this->orderId;
    }

    public function getTotal(): int
    {
        $total = 0;
        
        if (count($this->items) === 0) {
            return $total;
        }

        foreach($this->items as $item) {
            $total += $item->getTotal();
        }

        return $total;
    }

    public function addItem(Product $product, int $quantity): void
    {
        $item = new OrderItem(
            productId: $product->getId(),
            price: $product->getPrice(),
            quantity: $quantity
        );

        array_push($this->items, $item);
    }
}
