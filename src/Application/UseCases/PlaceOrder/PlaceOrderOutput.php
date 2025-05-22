<?php

declare(strict_types=1);

namespace Src\Application\UseCases\PlaceOrder;

class PlaceOrderOutput
{
    public function __construct(
        public readonly string $orderId,
    ) {}
}
