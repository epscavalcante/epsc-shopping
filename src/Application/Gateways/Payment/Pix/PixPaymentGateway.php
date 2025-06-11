<?php

declare(strict_types=1);

namespace Src\Application\Gateways\Payment\Pix;

interface PixPaymentGateway
{
    public function process(PixPaymentGatewayInput $input): PixPaymentGatewayOutput;
}
