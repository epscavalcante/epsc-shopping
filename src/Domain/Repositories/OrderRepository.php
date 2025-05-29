<?php

namespace Src\Domain\Repositories;

use Src\Domain\Entities\Order;

interface OrderRepository
{
    public function create(Order $order): void;

    public function getById (string $orderId): ?Order;

    public function getByIdOrFail (string $orderId): Order;
}