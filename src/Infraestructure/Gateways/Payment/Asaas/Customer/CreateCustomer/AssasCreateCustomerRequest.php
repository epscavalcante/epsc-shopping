<?php

declare(strict_types=1);

namespace Src\Infraestructure\Gateways\Payment\Asaas\Customer\CreateCustomer;

class AssasCreateCustomerRequest
{
    public function __construct(
        public readonly string $name,
        public readonly string $cpfCnpj,
        public readonly ?string $email = null,
        public readonly ?string $phone = null,
    ) {}
}
