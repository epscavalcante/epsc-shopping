<?php

declare(strict_types=1);

namespace Src\Application\UseCases\ProcessPayment\Pix;

final class PixProcessPaymentOutput
{
    public function __construct(
        public readonly string $paymentId,
        public readonly string $qrCode,
        public readonly string $copyPaste,
    ) {}
}
