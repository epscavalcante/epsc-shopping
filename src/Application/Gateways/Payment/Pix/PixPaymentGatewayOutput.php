<?php

declare(strict_types=1);

namespace Src\Application\Gateways\Payment\Pix;

class PixPaymentGatewayOutput
{
    public function __construct(
        public readonly string $gatewayName,
        public readonly string $gatewayTransactionId,
        public readonly string $qrCode,
        public readonly string $copyPaste,
    ) {}
}
