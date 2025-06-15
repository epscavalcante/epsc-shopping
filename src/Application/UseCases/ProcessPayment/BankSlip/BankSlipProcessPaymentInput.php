<?php

declare(strict_types=1);

namespace Src\Application\UseCases\ProcessPayment\BankSlip;

final class BankSlipProcessPaymentInput
{
    public function __construct(
        public readonly string $orderId,
    ) {}
}
