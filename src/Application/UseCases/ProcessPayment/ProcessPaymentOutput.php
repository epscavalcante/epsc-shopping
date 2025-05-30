<?php

declare(strict_types=1);

namespace Src\Application\UseCases\ProcessPayment;

final class ProcessPaymentOutput
{
    function __construct(
        public readonly string $paymentId,
    ) {}
}
