<?php

declare(strict_types=1);

namespace Src\Domain\Enums;

enum OrderStatusEnum: string
{
    case CREATED = 'CREATED';
    case WAITING_PAYMENT = 'WAITING_PAYMENT';
    case PAID = 'PAID';
}
