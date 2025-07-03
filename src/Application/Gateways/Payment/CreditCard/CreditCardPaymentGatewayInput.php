<?php

declare(strict_types=1);

namespace Src\Application\Gateways\Payment\CreditCard;

use DateTimeInterface;

class CreditCardPaymentGatewayInput
{
    public function __construct(
        public readonly int $amount,
        public readonly DateTimeInterface $dueDate,
        public readonly string $customerName,
        public readonly string $customerDocumentValue,
        public readonly ?string $creditCardName = null,
        public readonly ?string $creditCardNumber = null,
        public readonly ?string $creditCardExpiryMonth = null,
        public readonly ?string $creditCardExpiryYear = null,
        public readonly ?string $creditCardExpiryCCV = null,
        public readonly ?string $creditCardHolderName = null,
        public readonly ?string $creditCardHolderEmail = null,
        public readonly ?string $creditCardHolderDocumentValue = null,
        public readonly ?string $creditCardHolderPhone = null,
        public readonly ?string $creditCardHolderAddressPostalCode = null,
        public readonly ?string $creditCardHolderAddressNumber = null,
        public readonly ?string $creditCardHolderAddressComplement = null,
    ) {}
}