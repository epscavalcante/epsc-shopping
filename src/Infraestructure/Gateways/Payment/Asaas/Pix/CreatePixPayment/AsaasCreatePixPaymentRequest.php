<?php

declare(strict_types=1);

namespace Src\Infraestructure\Gateways\Payment\Asaas\Pix\CreatePixPayment;

use DateTimeInterface;

class AsaasCreatePixPaymentRequest
{
    public function __construct(
        public readonly string $customer,
        public readonly float $amount,
        public readonly DateTimeInterface $dueDate,
    ) {}
}
