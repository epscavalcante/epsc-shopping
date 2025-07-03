<?php

namespace Src\Domain\Repositories;

use Src\Domain\Entities\Payment;

interface PaymentRepository
{
    public function create(Payment $payment): void;

    public function getById (string $paymentId): ?Payment;

    public function getByIdOrFail (string $paymentId): Payment;
}