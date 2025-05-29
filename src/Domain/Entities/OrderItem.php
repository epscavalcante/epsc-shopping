<?php

declare(strict_types=1);

namespace Src\Domain\Entities;

use Exception;

class OrderItem
{
    public function __construct(
        public readonly string $productId,
        public readonly int $price,
        public readonly int $quantity,
    ) {

        if ($this->quantity <= 0) {
            throw new Exception('Invalid quantity');
        }
    }

    public function getTotal(): int
    {
        return $this->price * $this->quantity;
    }
}