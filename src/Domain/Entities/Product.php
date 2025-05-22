<?php

declare(strict_types=1);

namespace Src\Domain\Entities;

class Product
{
    public function __construct(
        private readonly string $productId,
        private readonly string $name,
        private readonly int $price
    ) {}

    public function getId(): string|int
    {
        return $this->productId;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getPrice(): int
    {
        return $this->price;
    }
}
