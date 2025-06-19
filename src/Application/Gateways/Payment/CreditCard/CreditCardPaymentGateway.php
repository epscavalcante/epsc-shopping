<?php

declare(strict_types=1);

namespace Src\Application\Gateways\Payment\CreditCard;

interface CreditCardPaymentGateway
{
    public function process(CreditCardPaymentGatewayInput $input): CreditCardPaymentGatewayOutput;
}
