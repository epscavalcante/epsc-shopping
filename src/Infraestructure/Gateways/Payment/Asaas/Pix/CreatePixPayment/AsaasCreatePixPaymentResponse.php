<?php

declare(strict_types=1);

namespace Src\Infraestructure\Gateways\Payment\Asaas\Pix\CreatePixPayment;

class AsaasCreatePixPaymentResponse
{
    public function __construct(
        public readonly string $paymentId,
    ) {}
}
