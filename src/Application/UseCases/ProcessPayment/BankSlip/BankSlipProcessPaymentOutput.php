<?php

declare(strict_types=1);

namespace Src\Application\UseCases\ProcessPayment\BankSlip;

final class BankSlipProcessPaymentOutput
{
    public function __construct(
        public readonly string $paymentId,
        public readonly string $barCode,
    ) {}
}
