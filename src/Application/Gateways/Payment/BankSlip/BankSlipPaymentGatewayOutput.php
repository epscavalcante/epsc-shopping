<?php

declare(strict_types=1);

namespace Src\Application\Gateways\Payment\BankSlip;

class BankSlipPaymentGatewayOutput
{
    public function __construct(
        public readonly string $gatewayName,
        public readonly string $gatewayTransactionId,
        public readonly string $barCode,
    ) {}
}
