<?php

declare(strict_types=1);

namespace Src\Infraestructure\Gateways\Payment\Asaas\Customer\CreateCustomer;

class AsaasCreateCustomerResponse
{
    public function __construct(
        public readonly string $customerId,
    ) {}
}
