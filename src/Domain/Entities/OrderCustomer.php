<?php

declare(strict_types=1);

namespace Src\Domain\Entities;

class OrderCustomer
{
    public function __construct(
        public readonly ?string $accountId = null,
        public readonly string $email,
        public readonly string $phone
    ) {}
}
