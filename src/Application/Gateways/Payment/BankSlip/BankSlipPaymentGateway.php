<?php

declare(strict_types=1);

namespace Src\Application\Gateways\Payment\BankSlip;

interface BankSlipPaymentGateway
{
    public function process(BankSlipPaymentGatewayInput $input): BankSlipPaymentGatewayOutput;
}
