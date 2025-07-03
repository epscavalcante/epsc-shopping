<?php

declare(strict_types=1);

namespace Src\Application\UseCases\ProcessPayment\CreditCard;

final class CreditCardProcessPaymentOutput
{
    public function __construct(
        public readonly string $paymentId,
    ) {}
}
