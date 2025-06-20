<?php

declare(strict_types=1);

namespace Src\Application\UseCases\ProcessPayment\CreditCard;

final class CreditCardProcessPaymentInput
{
    public function __construct(
        public readonly string $orderId,
        public readonly string $cardHolderName,
        public readonly string $cardNumber,
        public readonly string $cardExpiryMonth,
        public readonly string $cardExpiryYear,
        public readonly string $cardCCV,
        public readonly string $holderName,
        public readonly string $holderEmail,
        public readonly string $holderDocumentValue,
        public readonly string $holderPhone,
        public readonly string $holderAddressPostalCode,
        public readonly string $holderAddressNumber,
        public readonly ?string $holderAddressComplement = null
    ) {}
}
