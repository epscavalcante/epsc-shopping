<?php

declare(strict_types=1);

namespace Src\Application\UseCases\PlaceOrder;

class PlaceOrderInput
{
    public function __construct(
        public readonly array $items,
        public readonly ?string $accountId = null,
        public readonly ?string $email = null,
        public readonly ?string $phone = null,
    ) {}
}