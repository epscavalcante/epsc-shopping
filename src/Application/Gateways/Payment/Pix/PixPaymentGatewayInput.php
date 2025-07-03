<?php

declare(strict_types=1);

namespace Src\Application\Gateways\Payment\Pix;

use DateTimeInterface;

class PixPaymentGatewayInput
{
    public function __construct(
        public readonly int $amount,
        public readonly DateTimeInterface $dueDate,
        public readonly string $customerName,
        public readonly string $customerDocumentValue,
    ) {}
}