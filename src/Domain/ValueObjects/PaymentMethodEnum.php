<?php

declare(strict_types=1);

namespace Src\Domain\Entities;

enum PaymentMethodEnum: string
{
    case PIX = 'PIX';
    case BANK_SLIP = 'BANK_SLIP';
    case CREDIT_CARD = 'CREDIT_CARD';
}
