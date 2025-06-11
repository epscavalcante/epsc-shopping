<?php

declare(strict_types=1);

namespace Src\Application\UseCases\ProcessPayment;

use Src\Infraestructure\Gateways\Payment\PaymentGateway;

abstract class ProcessPayment
{
    abstract public function execute(ProcessPaymentInput $input): ProcessPaymentOutput;
}
