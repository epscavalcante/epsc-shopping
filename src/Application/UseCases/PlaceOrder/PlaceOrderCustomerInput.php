<?php

declare(strict_types=1);

namespace Src\Application\UseCases\PlaceOrder;

class PlaceOrderCustomerInput
{
    public function __construct(
        public readonly string $email,
        public readonly ?string $phone = null,
        public readonly ?string $name = null,
    ) {}
}