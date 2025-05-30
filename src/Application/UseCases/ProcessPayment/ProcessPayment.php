<?php

declare(strict_types=1);

namespace Src\Application\UseCases\ProcessPayment;

use Src\Infraestructure\Gateways\Payment\PaymentGateway;

class ProcessPayment
{
    public function __construct(
        private readonly PaymentGateway $paymentGateway
    ) {}

    public function execute(ProcessPaymentInput $input): ProcessPaymentOutput
    {
        return new ProcessPaymentOutput(paymentId: 'payment_id');
    }
}
