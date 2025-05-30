<?php

declare(strict_types=1);

namespace Src\Application\UseCases\ProcessPayment;

final class ProcessPaymentInput
{
    function __construct(
        public readonly string $orderId,
        public readonly string $paymentMethod,
    ) {}
}
