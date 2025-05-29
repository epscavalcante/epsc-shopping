<?php

declare(strict_types=1);

namespace Src\Domain\Entities;

class OrderItems
{
    /**
     * @var OrderItem[]
     */
    private array $items;

    public function __construct()
    {
        $this->items = [];
    }

    public function add(OrderItem $item)
    {
        array_push($this->items, $item);
    }

    /**
     * @return OrderItem[]
     */
    public function getItems(): array
    {
        return $this->items;
    }

    public function getTotal(): int
    {
        $total = 0;

        if (count($this->items) === 0) {
            return $total;
        }

        foreach ($this->items as $item) {
            $total += $item->getTotal();
        }

        return $total;
    }
}
