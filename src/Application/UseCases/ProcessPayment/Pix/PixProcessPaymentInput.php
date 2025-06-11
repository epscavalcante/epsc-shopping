<?php

declare(strict_types=1);

namespace Src\Application\UseCases\ProcessPayment\Pix;

final class PixProcessPaymentInput
{
    public function __construct(
        public readonly string $orderId,
    ) {}
}
