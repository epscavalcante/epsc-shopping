<?php

declare(strict_types=1);

namespace Src\Application\UseCases\PlaceOrder;

final class PlaceOrderInput
{
    function __construct(
        public readonly array $items,
        public readonly PlaceOrderCustomerInput $customer,
    ) {}

    public static function create(
        array $items,
        string $customerEmail,
        ?string $customerName = null,
        ?string $customerPhone = null,
    ) {
        return new PlaceOrderInput(
            items: $items,
            customer: new PlaceOrderCustomerInput(
                email: $customerEmail,
                phone: $customerPhone,
                name: $customerName
            ),
        );
    }
}
