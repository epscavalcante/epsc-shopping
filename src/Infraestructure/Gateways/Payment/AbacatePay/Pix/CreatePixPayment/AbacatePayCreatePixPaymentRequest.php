<?php

declare(strict_types=1);

namespace Src\Infraestructure\Gateways\Payment\AbacatePay\Pix\CreatePixPayment;

class AbacatePayCreatePixPaymentRequest
{
    public function __construct(
        public readonly int $amount,
        public readonly int $expiresIn,
        public readonly ?string $description,
    ) {}
}
