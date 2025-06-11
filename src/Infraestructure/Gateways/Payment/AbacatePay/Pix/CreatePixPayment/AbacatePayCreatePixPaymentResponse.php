<?php

declare(strict_types=1);

namespace Src\Infraestructure\Gateways\Payment\AbacatePay\Pix\CreatePixPayment;

class AbacatePayCreatePixPaymentResponse
{
    public function __construct(
        public readonly string $paymentId,
        public readonly string $status,
        public readonly string $copyPaste,
        public readonly string $qrCode,
    ) {}
}
