<?php

declare(strict_types=1);

namespace Src\Domain\Entities;

use Src\Domain\Enums\OrderStatusEnum;

class Order
{
    private OrderStatusEnum $status;

    private OrderItems $items;

    public function __construct(
        private readonly string $orderId,
        private readonly OrderCustomer $customer,
        ?OrderStatusEnum $status = OrderStatusEnum::CREATED,
    ) {
        $this->items = new OrderItems();
        $this->status = $status;
    }

    public function getId(): string
    {
        return $this->orderId;
    }

    public function getTotal(): int
    {
        return $this->items->getTotal();
    }

    public function getStatus(): string
    {
        return $this->status->value;
    }

    public function addItem(Product $product, int $quantity): void
    {
        $item = new OrderItem(
            productId: $product->getId(),
            price: $product->getPrice(),
            quantity: $quantity
        );

        $this->items->add($item);
    }

    public function restoreItem(string $productId, int $price, int $quantity): void
    {
        $item = new OrderItem(
            productId: $productId,
            price: $price,
            quantity: $quantity
        );

        $this->items->add($item);
    }

    /**
     * @return OrderItem[]
     */
    public function getItems(): array
    {
        return $this->items->getItems();
    }

    public function getCustomerName(): ?string
    {
        return $this->customer->name;
    }

    public function getCustomerEmail(): ?string
    {
        return $this->customer->email;
    }

    public function getCustomerPhone(): ?string
    {
        return $this->customer->phone;
    }
}
