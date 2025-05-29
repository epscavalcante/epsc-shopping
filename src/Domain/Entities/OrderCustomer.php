<?php

declare(strict_types=1);

namespace Src\Domain\Entities;

class OrderCustomer
{
    public function __construct(
        public readonly string $name,
        public readonly string $email,
        public readonly string $phone,
    ) {}
}
