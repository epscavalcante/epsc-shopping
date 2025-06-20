<?php

declare(strict_types=1);

namespace Src\Application\Gateways\Payment\CreditCard;

class CreditCardPaymentGatewayOutput
{
    public function __construct(
        public readonly string $gatewayName,
        public readonly string $gatewayTransactionId,
        public readonly string $creditCardToken,
        public readonly string $creditCardBrand,
        public readonly string $creditCardLastDigits,
    ) {}
}
